<template>
    <div class="xb-upload">
        <!-- 当前储存位置 -->
        <div class="xb-upload__storage">
            <span class="xb-upload__storage-label">储存位置</span>
            <span class="xb-upload__storage-value">{{ adapterTitle || name || '默认储存' }}</span>
        </div>

        <!-- 选择文件 -->
        <ElUpload ref="uploadRef" class="xb-upload__picker" drag multiple :auto-upload="false" :show-file-list="false"
            :accept="accept" :on-change="handleChange">
            <div class="xb-upload__picker-icon">
                <XbIcons icon="Upload" :size="32" />
            </div>
            <div class="xb-upload__picker-text">
                将文件拖到此处，或 <em>点击上传</em>
            </div>
            <div class="xb-upload__picker-tip">
                支持大文件上传、分片上传与断点续传，单个分片大小 {{ chunkSizeText }}
            </div>
        </ElUpload>

        <!-- 文件列表 -->
        <div class="xb-upload__list" v-if="files.length">
            <div class="xb-upload__item" v-for="item in files" :key="item.uid">
                <div class="xb-upload__item-body">
                    <div class="xb-upload__item-head">
                        <span class="xb-upload__item-name" :title="item.name">{{ item.name }}</span>
                        <span class="xb-upload__item-status" :class="`is-${item.status}`">
                            {{ statusText(item.status) }}
                        </span>
                    </div>
                    <ElProgress :percentage="Math.round(item.progress)" :status="progressStatus(item)"
                        :stroke-width="8" :show-text="false" />
                    <div class="xb-upload__item-meta">
                        <span>{{ formatSize(item.size) }}</span>
                        <span v-if="item.status === 'uploading' && item.speed > 0">{{ formatSize(item.speed) }}/s</span>
                        <span v-if="item.status === 'uploading'">{{ Math.round(item.progress) }}%</span>
                    </div>
                    <div class="xb-upload__item-error" v-if="item.error">{{ item.error }}</div>
                </div>
                <div class="xb-upload__item-actions">
                    <ElButton v-if="item.status === 'pending'" link type="primary" size="small"
                        @click="startOne(item)">开始</ElButton>
                    <ElButton v-if="item.status === 'uploading'" link type="warning" size="small"
                        @click="pauseOne(item)">暂停</ElButton>
                    <ElButton v-if="item.status === 'paused'" link type="primary" size="small"
                        @click="resumeOne(item)">继续</ElButton>
                    <ElButton v-if="item.status === 'error'" link type="primary" size="small"
                        @click="retryOne(item)">重试</ElButton>
                    <ElButton link type="danger" size="small" @click="removeOne(item)">移除</ElButton>
                </div>
            </div>
        </div>
        <div class="xb-upload__empty" v-else>
            暂未选择文件
        </div>

        <!-- 底部操作 -->
        <div class="xb-upload__footer">
            <div class="xb-upload__summary" v-if="files.length">
                共 {{ files.length }} 个文件，成功 {{ successCount }} 个
            </div>
            <div class="xb-upload__buttons">
                <ElButton @click="close">关闭</ElButton>
                <ElButton type="primary" :disabled="!pendingCount" @click="startAll">开始上传</ElButton>
            </div>
        </div>
    </div>
</template>

<script>
// 默认分片大小：5M
const DEFAULT_CHUNK_SIZE = 5 * 1024 * 1024;
// 分片并发数
const CONCURRENCY = 3;
// 分片失败重试次数
const MAX_RETRY = 2;

let uidSeed = 0;

export default {
    name: 'XbUpload',
    props: {
        // 储存适配器（来自 UploadController 的 name 参数）
        name: {
            type: String,
            default: '',
        },
        // 储存名称（展示用）
        adapterTitle: {
            type: String,
            default: '',
        },
        // 单文件上传接口
        uploadUrl: {
            type: String,
            default: '',
        },
        // 分片上传接口
        chunkUrl: {
            type: String,
            default: '',
        },
        // 分片大小
        chunkSize: {
            type: Number,
            default: DEFAULT_CHUNK_SIZE,
        },
        // 可选文件类型
        accept: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            files: [],
        }
    },
    computed: {
        // 分片大小（保证为有效正整数）
        chunkSizeValue() {
            const size = Number(this.chunkSize);
            return size > 0 ? size : DEFAULT_CHUNK_SIZE;
        },
        chunkSizeText() {
            return this.formatSize(this.chunkSizeValue);
        },
        // 待上传数量（含失败、暂停）
        pendingCount() {
            return this.files.filter((item) => ['pending', 'paused', 'error'].includes(item.status)).length;
        },
        successCount() {
            return this.files.filter((item) => item.status === 'success').length;
        },
    },
    beforeUnmount() {
        // 关闭时中断所有上传请求
        this.files.forEach((item) => this.abortControllers(item));
    },
    methods: {
        // 全局 http 实例
        http() {
            return this.$xbcode && this.$xbcode.$http;
        },
        // 提示
        notify(message, type = 'error') {
            if (message && this.$xbcode && this.$xbcode.useNotify) {
                this.$xbcode.useNotify(message, type);
            }
        },
        // 错误信息提取
        errMsg(e) {
            if (!e) return '上传失败';
            if (typeof e === 'string') return e;
            if (e.response && e.response.data && e.response.data.msg) return e.response.data.msg;
            return e.msg || e.message || '上传失败';
        },
        // 分片操作接口地址（start/chunk/finish）
        chunkApi(act) {
            const base = this.chunkUrl || '';
            if (!base) return '';
            const sep = base.indexOf('?') > -1 ? '&' : '?';
            return `${base}${sep}_act=${act}`;
        },
        // 文件大小格式化
        formatSize(bytes) {
            const size = Number(bytes) || 0;
            if (size < 1024) return `${size} B`;
            if (size < 1024 * 1024) return `${(size / 1024).toFixed(1)} KB`;
            if (size < 1024 * 1024 * 1024) return `${(size / 1024 / 1024).toFixed(1)} MB`;
            return `${(size / 1024 / 1024 / 1024).toFixed(2)} GB`;
        },
        statusText(status) {
            return {
                pending: '等待上传',
                uploading: '上传中',
                paused: '已暂停',
                success: '上传成功',
                error: '上传失败',
            }[status] || '';
        },
        progressStatus(item) {
            if (item.status === 'success') return 'success';
            if (item.status === 'error') return 'exception';
            return '';
        },
        // 选择文件
        handleChange(file) {
            if (!file || !file.raw) return;
            if (file.status && file.status !== 'ready') return;
            const raw = file.raw;
            // 同一次上传中避免重复添加相同文件
            const exists = this.files.some((item) =>
                item.name === raw.name
                && item.size === raw.size
                && item.lastModified === (raw.lastModified || 0)
                && item.status !== 'success'
            );
            if (exists) return;
            this.files.push({
                uid: ++uidSeed,
                raw,
                name: raw.name,
                size: raw.size,
                lastModified: raw.lastModified || 0,
                status: 'pending',
                progress: 0,
                loaded: 0,
                speed: 0,
                error: '',
                url: '',
                uploadId: '',
                key: '',
                total: 0,
                parts: [],
                partLoaded: [],
                controllers: [],
                _lastTime: 0,
                _lastLoaded: 0,
            });
        },
        // 开始全部
        startAll() {
            this.files.forEach((item) => {
                if (['pending', 'paused', 'error'].includes(item.status)) {
                    this.startOne(item);
                }
            });
        },
        startOne(item) {
            if (item.status === 'uploading' || item.status === 'success') return;
            item.status = 'pending';
            item.error = '';
            this.uploadFile(item);
        },
        // 暂停
        pauseOne(item) {
            if (item.status !== 'uploading') return;
            item.status = 'paused';
            this.abortControllers(item);
        },
        // 继续
        resumeOne(item) {
            if (item.status !== 'paused' && item.status !== 'error') return;
            item.status = 'pending';
            item.error = '';
            this.uploadFile(item);
        },
        // 重试
        retryOne(item) {
            if (item.status === 'uploading') return;
            item.status = 'pending';
            item.error = '';
            this.uploadFile(item);
        },
        // 移除
        removeOne(item) {
            this.abortControllers(item);
            const index = this.files.indexOf(item);
            if (index > -1) this.files.splice(index, 1);
        },
        // 中断当前文件的所有请求
        abortControllers(item) {
            (item.controllers || []).forEach((controller) => {
                try {
                    controller.abort();
                } catch (e) {
                    // 忽略中断异常
                }
            });
            item.controllers = [];
        },
        // 发送请求（统一处理中断器与进度）
        request(url, form, onUploadProgress, item) {
            const http = this.http();
            if (!http) return Promise.reject(new Error('请求实例不可用'));
            if (!url) return Promise.reject(new Error('上传接口地址为空'));
            const controller = typeof AbortController !== 'undefined' ? new AbortController() : null;
            if (controller) item.controllers.push(controller);
            const config = {};
            if (onUploadProgress) config.onUploadProgress = onUploadProgress;
            if (controller) config.signal = controller.signal;
            return http.post(url, form, config).finally(() => {
                if (controller) {
                    const index = item.controllers.indexOf(controller);
                    if (index > -1) item.controllers.splice(index, 1);
                }
            });
        },
        // 上传单个文件
        async uploadFile(item) {
            if (!['pending', 'uploading'].includes(item.status)) return;
            item.status = 'uploading';
            item.error = '';
            item.loaded = 0;
            item.speed = 0;
            item._lastTime = Date.now();
            item._lastLoaded = 0;
            try {
                if (item.size <= 0) {
                    throw new Error('文件内容为空');
                }
                if (item.size <= this.chunkSizeValue) {
                    await this.uploadSingle(item);
                } else {
                    await this.uploadChunked(item);
                }
            } catch (e) {
                // 暂停引起的异常不视为失败
                if (item.status === 'paused') return;
                item.status = 'error';
                item.error = this.errMsg(e);
                this.notify(item.error, 'error');
                return;
            }
            item.status = 'success';
            item.progress = 100;
            // 上传成功后刷新列表
            this.$emit('refresh');
        },
        // 小文件直传
        async uploadSingle(item) {
            const form = new FormData();
            form.append('file', item.raw, item.name);
            form.append('adapter', this.name || '');
            const res = await this.request(this.uploadUrl, form, (e) => {
                item.loaded = e.loaded || 0;
                item.progress = item.size ? Math.min(99, (item.loaded / item.size) * 100) : 0;
                this.updateSpeed(item);
            }, item);
            const data = (res && res.data) || {};
            const url = data.url || data.value || '';
            if (!url) {
                throw new Error((res && res.msg) || '文件上传失败');
            }
            item.url = url;
            item.progress = 100;
        },
        // 大文件分片上传（支持断点续传）
        async uploadChunked(item) {
            const size = this.chunkSizeValue;
            // 1.初始化，获取服务端已上传的分片，用于断点续传
            const startForm = new FormData();
            // 使用文件指纹作为 name，确保同一文件续传、不同文件重新开始
            startForm.append('name', `${item.name}|${item.size}|${item.lastModified}|${size}`);
            startForm.append('adapter', this.name || '');
            const startRes = await this.request(this.chunkApi('start'), startForm, null, item);
            const startData = (startRes && startRes.data) || {};
            const uploadId = startData.uploadId || '';
            const key = startData.key || '';
            if (!uploadId || !key) {
                throw new Error((startRes && startRes.msg) || '初始化分片上传失败');
            }
            item.uploadId = uploadId;
            item.key = key;
            const total = Math.ceil(item.size / size);
            item.total = total;
            if (!item.parts || item.parts.length !== total) {
                item.parts = new Array(total).fill('');
                item.partLoaded = new Array(total).fill(0);
            }
            // 2.合并服务端已存在的分片
            const serverParts = Array.isArray(startData.partList) ? startData.partList : [];
            serverParts.forEach((part) => {
                const num = Number(part && part.partNumber);
                if (num >= 1 && num <= total && part.eTag) {
                    item.parts[num - 1] = part.eTag;
                    item.partLoaded[num - 1] = this.partSize(num, item, size);
                }
            });
            // 3.计算待上传分片
            const todo = [];
            for (let i = 1; i <= total; i++) {
                if (!item.parts[i - 1]) todo.push(i);
            }
            this.updateProgress(item);
            // 4.按并发数分批上传
            for (let i = 0; i < todo.length; i += CONCURRENCY) {
                if (item.status === 'paused') return;
                const batch = todo.slice(i, i + CONCURRENCY);
                await Promise.all(batch.map((num) => this.uploadPart(item, num, size)));
            }
            if (item.status === 'paused') return;
            // 5.校验分片完整性
            const missing = item.parts.findIndex((eTag) => !eTag);
            if (missing > -1) {
                throw new Error(`第 ${missing + 1} 个分片缺失，请重试`);
            }
            // 6.请求合并分片
            const finishForm = new FormData();
            finishForm.append('key', key);
            finishForm.append('filename', item.name);
            finishForm.append('adapter', this.name || '');
            item.parts.forEach((eTag, index) => {
                finishForm.append(`partList[${index}][eTag]`, eTag);
            });
            const finishRes = await this.request(this.chunkApi('finish'), finishForm, null, item);
            const finishData = (finishRes && finishRes.data) || {};
            const url = finishData.url || finishData.value || '';
            if (!url) {
                throw new Error((finishRes && finishRes.msg) || '合并分片失败');
            }
            item.url = url;
        },
        // 计算分片大小
        partSize(num, item, size) {
            const start = (num - 1) * size;
            return Math.min(size, item.size - start);
        },
        // 上传单个分片（含重试）
        async uploadPart(item, num, size) {
            const start = (num - 1) * size;
            const end = Math.min(start + size, item.size);
            const blob = item.raw.slice(start, end);
            let lastError = null;
            for (let attempt = 0; attempt <= MAX_RETRY; attempt++) {
                if (item.status === 'paused') return;
                try {
                    const form = new FormData();
                    form.append('uploadId', item.uploadId);
                    form.append('key', item.key);
                    form.append('partNumber', num);
                    form.append('partSize', blob.size);
                    form.append('adapter', this.name || '');
                    form.append('file', blob, `${item.name}.part${num}`);
                    const res = await this.request(this.chunkApi('chunk'), form, (e) => {
                        item.partLoaded[num - 1] = e.loaded || 0;
                        this.updateProgress(item);
                    }, item);
                    const data = (res && res.data) || {};
                    if (!data.eTag) {
                        throw new Error((res && res.msg) || `分片 ${num} 上传失败`);
                    }
                    item.parts[num - 1] = data.eTag;
                    item.partLoaded[num - 1] = blob.size;
                    this.updateProgress(item);
                    return;
                } catch (e) {
                    if (item.status === 'paused') return;
                    lastError = e;
                }
            }
            throw lastError || new Error(`分片 ${num} 上传失败`);
        },
        // 更新总分片进度
        updateProgress(item) {
            const loaded = (item.partLoaded || []).reduce((sum, n) => sum + (Number(n) || 0), 0);
            item.loaded = loaded;
            item.progress = item.size ? Math.min(99, (loaded / item.size) * 100) : 0;
            this.updateSpeed(item);
        },
        // 更新上传速度
        updateSpeed(item) {
            const now = Date.now();
            const elapsed = now - (item._lastTime || now);
            if (elapsed < 500) return;
            const delta = item.loaded - (item._lastLoaded || 0);
            item.speed = delta > 0 ? (delta / elapsed) * 1000 : 0;
            item._lastTime = now;
            item._lastLoaded = item.loaded;
        },
        // 关闭弹窗
        close() {
            this.$emit('close');
        },
    },
}
</script>

<style lang="scss" scoped>
.xb-upload {
    display: flex;
    flex-direction: column;
    height: 100%;
    box-sizing: border-box;
    padding: 16px;

    &__storage {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        font-size: 13px;

        &-label {
            color: #909399;
        }

        &-value {
            color: #409eff;
            font-weight: 600;
        }
    }

    &__picker {
        flex-shrink: 0;
        width: 100%;

        :deep(.el-upload) {
            width: 100%;
        }

        :deep(.el-upload-dragger) {
            padding: 20px 16px;
        }
    }

    &__picker-icon {
        color: #c0c4cc;
        line-height: 1;
        margin-bottom: 8px;

        :deep(svg) {
            display: inline-block;
        }
    }

    &__picker-text {
        color: #606266;
        font-size: 14px;

        em {
            color: #409eff;
            font-style: normal;
        }
    }

    &__picker-tip {
        margin-top: 6px;
        color: #909399;
        font-size: 12px;
    }

    &__list {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        margin-top: 12px;
    }

    &__item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border: 1px solid var(--el-border-color-lighter, #ebeef5);
        border-radius: 6px;

        & + & {
            margin-top: 8px;
        }

        &-body {
            flex: 1;
            min-width: 0;
        }

        &-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 6px;
        }

        &-name {
            flex: 1;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #303133;
            font-size: 13px;
        }

        &-status {
            flex-shrink: 0;
            font-size: 12px;

            &.is-pending {
                color: #909399;
            }

            &.is-uploading {
                color: #409eff;
            }

            &.is-paused {
                color: #e6a23c;
            }

            &.is-success {
                color: #67c23a;
            }

            &.is-error {
                color: #f56c6c;
            }
        }

        &-meta {
            display: flex;
            gap: 12px;
            margin-top: 4px;
            color: #909399;
            font-size: 12px;
        }

        &-error {
            margin-top: 4px;
            color: #f56c6c;
            font-size: 12px;
            word-break: break-all;
        }

        &-actions {
            flex-shrink: 0;
            display: flex;
            align-items: center;
        }
    }

    &__empty {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #c0c4cc;
        font-size: 13px;
    }

    &__footer {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid var(--el-border-color-lighter, #ebeef5);
    }

    &__summary {
        color: #909399;
        font-size: 13px;
    }

    &__buttons {
        display: flex;
        gap: 8px;
        margin-left: auto;
    }
}
</style>