<template>
    <div class="upload-container">
        <ElUpload class="upload-wrap" drag multiple :auto-upload="false" :show-file-list="false"
            :on-change="handleChange">
            <div class="upload-icon">
                <svg viewBox="0 0 1024 1024" width="48" height="48" xmlns="http://www.w3.org/2000/svg">
                    <path fill="currentColor"
                        d="M544 864V672h128L512 320 352 672h128v192H256v-240H128L512 128l384 496H672v240z" />
                </svg>
            </div>
            <div class="el-upload__text">
                将文件拖放到此处或 <em>点击上传</em>
            </div>
            <template #tip>
                <div class="el-upload__tip">
                    支持大文件分片上传，单个分片 {{ chunkSizeText }}，中断后可续传
                </div>
            </template>
        </ElUpload>

        <div v-if="tasks.length" class="upload-list">
            <div class="upload-list__header">
                <span>上传列表</span>
                <span class="upload-list__clear" @click="clearFinished">
                    清除已完成
                </span>
            </div>
            <div v-for="task in tasks" :key="task.uid" class="upload-item">
                <div class="upload-item__info">
                    <span class="upload-item__name" :title="task.name">{{ task.name }}</span>
                    <span class="upload-item__size">{{ formatSize(task.size) }}</span>
                </div>
                <ElProgress :percentage="task.percent" :status="progressStatus(task)"
                    :stroke-width="10" />
                <div class="upload-item__footer">
                    <span class="upload-item__status" :class="'is-' + task.status">
                        {{ statusText(task) }}
                    </span>
                    <span class="upload-item__actions">
                        <ElButton v-if="task.status === 'uploading'" link type="warning"
                            @click="pauseTask(task)">暂停</ElButton>
                        <ElButton v-if="task.status === 'paused' || task.status === 'error'" link type="primary"
                            @click="resumeTask(task)">{{ task.status === 'error' ? '重试' : '继续' }}</ElButton>
                        <ElButton link type="danger" @click="removeTask(task)">删除</ElButton>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
// 分片大小：统一固定，保证断点续传时 eTag 可复现（后端 eTag 依赖 uploadId + partSize）
const CHUNK_SIZE = 5 * 1024 * 1024;
// 超过该大小走分片上传，否则走普通上传
const CHUNK_THRESHOLD = CHUNK_SIZE;
// 断点续传记录在 localStorage 的键前缀
const RESUME_PREFIX = 'xbcode_upload_resume_';

export default {
    props: {
        // 当前储存引擎标识，由远程视图 query（adapter）注入；为空时后端按默认引擎处理
        adapter: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            tasks: [],
            chunkSize: CHUNK_SIZE,
            seq: 0,
        }
    },
    computed: {
        chunkSizeText() {
            return this.formatSize(this.chunkSize);
        },
    },
    methods: {
        // 归一化后端下发的上传地址（可能是 host[:port]/path 形式，缺少协议）
        resolveUrl(url) {
            const u = String(url || '');
            if (!u) return '';
            if (/^https?:\/\//i.test(u)) return u;
            if (u.startsWith('//')) return window.location.protocol + u;
            const idx = u.indexOf('/');
            const first = idx === -1 ? u : u.slice(0, idx);
            if (idx > 0 && (first.indexOf(':') > -1 || first.indexOf('.') > -1)) {
                return window.location.protocol + '//' + u;
            }
            return u.startsWith('/') ? u : '/' + u;
        },
        // 获取上传接口地址
        getApi() {
            const info = (this.$xbcode && this.$xbcode.siteApp && this.$xbcode.siteApp.siteInfo) || {};
            const api = info.upload_api || {};
            const upload = this.resolveUrl(api.upload || '');
            const chunk = this.resolveUrl(api.chunk || '');
            return { upload, chunk };
        },
        // 解析当前要上传到的储存引擎（优先 props，其次 query，缺省交后端用默认引擎）
        getAdapter() {
            const attrs = this.$attrs || {};
            const route = this.$route || {};
            const query = route.query || {};
            return String(this.adapter || attrs.adapter || query.adapter || '');
        },
        // 选择文件后接管上传
        handleChange(file) {
            if (!file || !file.raw) return;
            const raw = file.raw;
            const uid = 'u' + (++this.seq) + '_' + Date.now();
            const task = {
                uid,
                name: raw.name,
                size: raw.size,
                raw,
                percent: 0,
                status: 'waiting',
                paused: false,
                canceled: false,
                uploadId: '',
                key: '',
                parts: {},   // { partNumber: eTag }
                error: '',
            };
            this.tasks.push(task);
            // 取回响应式代理，保证后续进度更新能触发视图刷新
            const item = this.tasks[this.tasks.length - 1];
            this.startTask(item);
        },
        // 开始上传
        async startTask(task) {
            if (task.status === 'uploading') return;
            task.status = 'uploading';
            task.error = '';
            try {
                const api = this.getApi();
                const adapter = this.getAdapter();
                if (task.size <= CHUNK_THRESHOLD) {
                    await this.uploadSingle(task, api.upload, adapter);
                } else {
                    await this.uploadChunked(task, api.chunk, adapter);
                }
                if (task.canceled) return;
                if (task.paused) return; // 已暂停，等待用户继续
                task.percent = 100;
                task.status = 'success';
                this.clearResume(task);
                this.$emit('success', { name: task.name, size: task.size });
            } catch (e) {
                if (task.canceled) return;
                task.status = 'error';
                task.error = this.errMsg(e);
            }
        },
        // 普通上传（小文件）
        uploadSingle(task, url, adapter) {
            const form = new FormData();
            form.append('file', task.raw);
            if (adapter) form.append('adapter', adapter);
            return this.$xbcode.$http.post(url, form, {
                onUploadProgress: (e) => {
                    if (e && e.total) {
                        task.percent = Math.min(99, Math.floor((e.loaded / e.total) * 100));
                    }
                },
            }).then(this.ensureOk);
        },
        // 分片上传（大文件，支持断点续传）
        async uploadChunked(task, chunkUrl, adapter) {
            const resume = this.loadResume(task);
            if (resume && resume.uploadId && resume.key) {
                task.uploadId = resume.uploadId;
                task.key = resume.key;
                task.parts = Object.assign({}, resume.parts || {});
            } else {
                await this.chunkStart(task, chunkUrl, adapter);
            }
            const total = Math.ceil(task.size / CHUNK_SIZE);
            for (let i = 0; i < total; i++) {
                if (task.canceled) return;
                if (task.paused) {
                    task.status = 'paused';
                    this.saveResume(task); // 保留已完成分片，便于续传
                    return;
                }
                const partNumber = String(i + 1);
                if (task.parts[partNumber]) {
                    // 已存在该分片，跳过
                    task.percent = Math.floor(((i + 1) / total) * 100);
                    continue;
                }
                const start = i * CHUNK_SIZE;
                const blob = task.raw.slice(start, Math.min(start + CHUNK_SIZE, task.size));
                const eTag = await this.chunkUpload(task, chunkUrl, partNumber, blob, i, total, adapter);
                task.parts[partNumber] = eTag;
                task.percent = Math.floor(((i + 1) / total) * 100);
                this.saveResume(task);
            }
            if (task.paused) {
                task.status = 'paused';
                this.saveResume(task);
                return;
            }
            await this.chunkFinish(task, chunkUrl, adapter);
        },
        // 开启分片任务
        chunkStart(task, chunkUrl, adapter) {
            const body = { name: task.name };
            if (adapter) body.adapter = adapter;
            return this.$xbcode.$http.post(chunkUrl + '?_act=start', body)
                .then(this.ensureOk)
                .then((res) => {
                    const data = (res && res.data) || {};
                    task.uploadId = data.uploadId || '';
                    task.key = data.key || '';
                    task.parts = {};
                    if (!task.uploadId || !task.key) {
                        throw new Error('创建分片任务失败');
                    }
                });
        },
        // 上传单个分片
        chunkUpload(task, chunkUrl, partNumber, blob, index, total, adapter) {
            const form = new FormData();
            form.append('uploadId', task.uploadId);
            form.append('key', task.key);
            form.append('partNumber', partNumber);
            form.append('partSize', String(CHUNK_SIZE));
            if (adapter) form.append('adapter', adapter);
            form.append('file', blob, task.name + '.part' + partNumber);
            return this.$xbcode.$http.post(chunkUrl + '?_act=chunk', form, {
                onUploadProgress: (e) => {
                    if (e && e.total) {
                        const loaded = index * CHUNK_SIZE + e.loaded;
                        task.percent = Math.min(99, Math.floor((loaded / task.size) * 100));
                    }
                },
            }).then(this.ensureOk).then((res) => {
                const data = (res && res.data) || {};
                if (!data.eTag) {
                    throw new Error('分片上传失败');
                }
                return data.eTag;
            });
        },
        // 合并分片完成上传
        chunkFinish(task, chunkUrl, adapter) {
            const partList = Object.keys(task.parts)
                .sort((a, b) => Number(a) - Number(b))
                .map((n) => ({ partNumber: Number(n), eTag: task.parts[n] }));
            const body = {
                key: task.key,
                filename: task.name,
                partList: JSON.stringify(partList),
            };
            if (adapter) body.adapter = adapter;
            return this.$xbcode.$http.post(chunkUrl + '?_act=finish', body).then(this.ensureOk);
        },
        // 统一校验响应状态
        ensureOk(res) {
            if (!res || Number(res.status) !== 0) {
                const msg = (res && res.msg) || '上传失败';
                return Promise.reject(new Error(msg));
            }
            return res;
        },
        // 暂停：当前分片传完后停止
        pauseTask(task) {
            if (task.status !== 'uploading') return;
            task.paused = true;
            task.status = 'paused';
            this.saveResume(task);
        },
        // 继续/重试
        resumeTask(task) {
            if (task.status === 'uploading') return;
            task.paused = false;
            this.startTask(task);
        },
        // 删除任务
        removeTask(task) {
            task.canceled = true;
            this.clearResume(task);
            const idx = this.tasks.findIndex((t) => t.uid === task.uid);
            if (idx > -1) {
                this.tasks.splice(idx, 1);
            }
        },
        // 清除已完成
        clearFinished() {
            this.tasks = this.tasks.filter((t) => t.status !== 'success');
        },
        // 断点记录键（区分储存引擎，避免跨引擎复用分片）
        resumeKey(task) {
            return RESUME_PREFIX + this.getAdapter() + '_' + task.name + '_' + task.size + '_' + task.raw.lastModified;
        },
        // 从 localStorage 读取断点记录
        loadResume(task) {
            try {
                const str = window.localStorage.getItem(this.resumeKey(task));
                return str ? JSON.parse(str) : null;
            } catch (e) {
                return null;
            }
        },
        // 保存断点记录
        saveResume(task) {
            if (!task.uploadId || !task.key) return;
            try {
                window.localStorage.setItem(this.resumeKey(task), JSON.stringify({
                    uploadId: task.uploadId,
                    key: task.key,
                    parts: task.parts,
                }));
            } catch (e) { /* 忽略存储异常 */ }
        },
        // 清除断点记录
        clearResume(task) {
            try {
                window.localStorage.removeItem(this.resumeKey(task));
            } catch (e) { /* 忽略存储异常 */ }
        },
        // 错误信息提取（fail/异常会 reject 并携带 {msg}）
        errMsg(e) {
            if (!e) return '上传失败';
            if (typeof e === 'string') return e;
            return e.msg || e.message || '上传失败';
        },
        // 状态文案
        statusText(task) {
            if (task.status === 'success') return '上传完成';
            if (task.status === 'error') return task.error || '上传失败';
            if (task.status === 'paused') return '已暂停，可继续续传';
            if (task.status === 'uploading') return '上传中 ' + task.percent + '%';
            return '等待上传';
        },
        // 进度条状态
        progressStatus(task) {
            if (task.status === 'success') return 'success';
            if (task.status === 'error') return 'exception';
            if (task.status === 'paused') return 'warning';
            return '';
        },
        // 文件大小格式化
        formatSize(size) {
            if (!size && size !== 0) return '0 B';
            const units = ['B', 'KB', 'MB', 'GB', 'TB'];
            let i = 0;
            let n = Number(size);
            while (n >= 1024 && i < units.length - 1) {
                n = n / 1024;
                i++;
            }
            return (i === 0 ? n : n.toFixed(2)) + ' ' + units[i];
        },
    },
}
</script>

<style scoped>
.upload-container {
    height: 100%;
    padding: 15px;
    overflow: auto;
    box-sizing: border-box;
}

.upload-wrap {
    display: block;
}

.upload-icon {
    color: #c0c4cc;
    margin-bottom: 8px;
    line-height: 1;
}

.upload-list {
    margin-top: 20px;
}

.upload-list__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
    color: #606266;
    padding-bottom: 8px;
    border-bottom: 1px solid #ebeef5;
}

.upload-list__clear {
    color: #409eff;
    cursor: pointer;
    font-size: 13px;
}

.upload-item {
    padding: 12px 0;
    border-bottom: 1px solid #f5f7fa;
}

.upload-item__info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
    font-size: 13px;
}

.upload-item__name {
    color: #303133;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 70%;
}

.upload-item__size {
    color: #909399;
}

.upload-item__footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 4px;
}

.upload-item__status {
    font-size: 12px;
    color: #909399;
}

.upload-item__status.is-success {
    color: #67c23a;
}

.upload-item__status.is-error {
    color: #f56c6c;
}

.upload-item__status.is-paused {
    color: #e6a23c;
}

.upload-item__status.is-uploading {
    color: #409eff;
}
</style>