<template>
    <div class="import-container">
        <div class="body-container">
            <div class="progress">
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
        plugin: {
            type: Object,
        },
        closeCallback: {
            type: Function,
            default: () => { }
        },
    },
    data() {
        return {
            progress: {
                step: '',
                plugin: null,
                list: [],
            },
        }
    },
    mounted() {
        const _this = this
        _this.progress.plugin = _this.$props.plugin
        _this.addStep(`获取「${_this.progress.plugin.title}」信息成功...`, 'detail')
        setTimeout(() => {
            const title = _this.progress.plugin.title
            _this.addStep(`准备卸载「${title}」...`, 'script')
            setTimeout(() => {
                _this.progressIng()                
            }, 1500);
        }, 1500);
    },
    methods: {
        progressIng() {
            const _this = this;
            useVue.$axios.put('app/xbPlugins/admin/Plugins/uninstall', {
                step: this.progress.step,
                name: this.progress.plugin.name,
                version_name: this.progress.plugin.version_name,
                version: this.progress.plugin.version,
            }).then(res => {
                if (res?.data?.next === '') {
                    this.addStep(res?.msg ?? '插件安装完成...', 'success', 'success')
                    setTimeout(() => {
                        this.addStep('窗口将于5秒后，自动关闭...', 'success', 'success')
                    }, 800);
                    setTimeout(() => {
                        _this.closeCallback()
                        _this.$emit('close')
                    }, 5800);
                } else if (res?.data?.next) {
                    this.addStep(res?.msg ?? '未知消息...', res?.data?.next)
                    this.progress.step = res?.data?.next
                    setTimeout(() => {
                        this.progressIng()
                    }, 1000);
                } else {
                    this.addStep(res?.msg ?? '未知消息!...', 'fail', 'error')
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
        },
    },
}
</script>

<style lang="scss" scoped>
.import-container {
    .body-container {
        height: 450px;

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