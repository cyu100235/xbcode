<template>
    <div class="plugin-container">
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
        plugin: Object,
        steps: Array,
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
        this.progress.plugin = this.plugin;
        this.addStep(`正在安装 【${this.plugin.title}】 插件...`, 'install')
        setTimeout(() => {
            // 取第一个数组
            const step = this.steps[0]?.name ?? null;
            this.progress.step = step;
            this.install();
        }, 500);
    },
    methods: {
        install() {
            const step = this.steps.find(item => item.name === this.progress.step);
            if (!step) {
                this.addStep('未知步骤', 'fail', 'error');
                return;
            }
            this.$xbcode.axios.post('app/xbCode/admin/Plugins/install', {
                step: this.progress.step,
                name: this.progress.plugin.name,
                version: this.progress.plugin.version,
            }).then(res => {
                if (step.next === 'success') {
                    this.addStep(step.text ?? '插件安装完成...', 'success', 'success');
                    setTimeout(() => {
                        this.addStep('窗口将于3秒后关闭...', 'success', 'success');
                    }, 800);
                    setTimeout(() => {
                        this.$emit('refresh');
                        this.$emit('close')
                    }, 3800);
                } else if (step.next) {
                    this.addStep(res?.data.text ?? '插件安装中...', step.next)
                    this.progress.step = step.next
                    setTimeout(() => {
                        this.install()
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
.plugin-container {
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