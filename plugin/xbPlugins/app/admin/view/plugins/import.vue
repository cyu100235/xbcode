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
                <div :class="item.type" v-for="(item,index) in progress.list" :key="index">
                    - {{ item.text }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        closeCallback: {
            type: Function,
            default: () => {}
        },
    },
    data() {
        return {
            progress: {
                step: '',
                plugin: null,
                list: [],
            },
            uploadProps: {
                action: '/app/xbPlugins/admin/Plugins/import',
                showFileList: false,
                drag: true,
                accept: '.zip',
                onSuccess: (res) => {
                    if (res?.code === 200) {
                        if (res?.data?.text) {
                            this.addStep(res?.data?.text, 'info')
                        }
                        this.addStep('开始解压插件...', 'unzip')
                        this.plugin = res?.data?.plugin
                        setTimeout(() => {
                            this.importPlugin();
                        }, 500);
                    } else {
                        this.addStep(res?.msg ?? '未知错误', 'fail', 'error')
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
        importPlugin() {
            const _this = this;
            useVue.$axios.post('app/xbPlugins/admin/Plugins/import', {
                step: this.progress.step,
                ...this.plugin
            }).then(res => {
                if (res?.data?.next === '') {
                    this.addStep(res?.data?.text ?? '插件安装完成...', 'success', 'success')
                    setTimeout(() => {
                        this.addStep('窗口将于5秒后，自动关闭...', 'success', 'success')
                    }, 800);
                    setTimeout(() => {
                        _this.closeCallback()
                        _this.$emit('close')
                    }, 5800);
                } else if (res?.data?.next) {
                    this.addStep(res?.data.text ?? '插件安装中...', res?.data?.next)
                    this.progress.step = res?.data?.next
                    setTimeout(() => {
                        this.importPlugin()                        
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
    .body-container {
        height: 450px;
        .xb-upload{
            padding: 20px;
        }
        .progress {
            height: 100%;
            color: #fff;
            padding: 20px;
            font-size: 14px;
            line-height: 25px;
            background-color: #0f1624;
            .text{
                color: #fff;
            }
            .success{
                color: #50bc1a;
            }
            .error{
                color: #f56c6c;
            }
        }
    }
}
</style>