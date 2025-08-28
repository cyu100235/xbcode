<template>
    <div class="import-container">
        <div class="body-container">
            <el-upload class="xb-upload" v-bind="uploadProps" v-if="!progress.step">
                <div class="description">
                    <xbIcons icon="Upload" size="32" />
                    <div class="description-text">将插件压缩包拖到此处，或点击上传</div>
                </div>
            </el-upload>
            <div class="progress" v-else>
                <div :class="item.type" v-for="(item, index) in progress.list" :key="index">
                    - {{ item.text }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        steps: Array,
    },
    data() {
        return {
            progress: {
                step: '',
                active: 0,
                plugin: null,
                list: [],
            },
            uploadProps: {
                action: `${this.$xbcode.baseURL}/app/xbCode/admin/Plugins/import`,
                showFileList: false,
                drag: true,
                accept: '.zip',
                data: {
                    step: 'upload',
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Authorization': this.$xbcode.token,
                },
                onSuccess: (res) => {
                    if (res?.status === 0 && res?.data?.plugin) {
                        this.progress.plugin = res.data.plugin;
                        this.addStep(res?.data?.text ?? '插件上传成功，开始解压...', 'install')
                        setTimeout(() => {
                            this.progress.step = 'unzip';
                            this.import();
                        }, 500);
                    } else {
                        this.addStep(res?.msg ?? '未知异常错误', 'fail', 'error')
                    }
                },
                onError: () => {
                    this.addStep('插件上传失败', 'fail', 'error')
                },
                beforeUpload: () => {
                    this.addStep('上传插件中...', 'upload')
                },
            },
        }
    },
    mounted() {
    },
    methods: {
        import() {            
            const step = this.steps.find(item => item?.name === this.progress.step);
            if (!step) {
                this.addStep('未知步骤', 'fail', 'error');
                return;
            }
            this.$xbcode.axios.post('app/xbCode/admin/Plugins/import', {
                step: this.progress.step,
                name: this.progress.plugin.name,
                version: this.progress.plugin.version,
            }).then(res => {
                if (step.next === 'success') {
                    this.addStep(step.text ?? '插件导入完成...', 'success', 'success');
                    setTimeout(() => {
                        this.addStep('窗口将于3秒后关闭...', 'success', 'success');
                    }, 800);
                    setTimeout(() => {
                        this.$emit('refresh');
                        this.$emit('close')
                    }, 3800);
                } else if (step.next) {
                    this.addStep(res?.data.text ?? '插件导入中...', step.next)
                    setTimeout(() => {
                        this.progress.step = step.next
                        this.import()
                    }, 500);
                } else {
                    this.addStep(res?.data?.text ?? res?.msg, 'fail', 'error')
                }
            }).catch(err => {
                this.addStep(err?.msg ?? '未知错误', 'fail', 'error')
            })
        },
        addStep(text, step, type = 'text') {
            this.progress.step = step
            this.progress.list.push({
                text,
                type
            })
        }
    },
}
</script>

<style lang="scss" scoped>
.import-container {
    height: 100%;

    .body-container {
        height: 100%;

        .xb-upload {
            padding: 20px;
        }

        .progress {
            height: 100%;
            color: #fff;
            padding: 20px;
            font-size: 14px;
            line-height: 25px;
            background-color: #0f1624;

            .text {
                color: #fff;
            }

            .success {
                color: #50bc1a;
            }

            .error {
                color: #f56c6c;
            }
        }
    }
}
</style>