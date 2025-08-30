<template>
    <div class="import-container">
        <div class="body-container">
            <div class="progress" v-if="progress.step">
                <div :class="item.type" v-for="(item, index) in progress.list" :key="index">
                    - {{ item.text }}
                </div>
            </div>
            <!-- 现实表单 -->
            <div class="form-container" v-else>
                <div class="tag-container">
                    <el-alert type="primary" title="温馨提示" :closable="false">
                        <div class="tag-content">
                            <div>结构数据路径：plugin/插件/install.sql</div>
                            <div>菜单数据路径：plugin/插件/config/menu.php</div>
                            <div>字典数据路径：plugin/插件/config/dict.php</div>
                            <div>定时任务路径：plugin/插件/config/crontab.php</div>
                        </div>
                    </el-alert>
                </div>
                <el-form label-position="top" label-width="80px">
                    <el-form-item label="导出类型">
                        <el-checkbox-group v-model="checkedTypes.active">
                            <el-checkbox v-for="item in checkedTypes.list" :key="item" :value="item.name">
                                {{ item.title }}
                            </el-checkbox>
                        </el-checkbox-group>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="hanldSubmit">
                            确定导出
                        </el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props:{
        name:String
    },
    data() {
        return {
            plugin: null,
            progress: {
                step: '',
                list: [],
            },
            checkedTypes: {
                active: ['sql', 'menus', 'dict', 'crontab'],
                list: [
                    {
                        title: '插件表结构',
                        name: 'sql',
                    },
                    {
                        title: '插件菜单',
                        name: 'menus',
                    },
                    {
                        title: '数据字典',
                        name: 'dict',
                    },
                    {
                        title: '定时任务',
                        name: 'crontab',
                    },
                ],
            },
        }
    },
    created() {        
        this.getDetail();
    },
    methods: {
        hanldExport(name) {
            const activeData = this.checkedTypes.list.find((item) => item.name === name);
            if (!activeData) {
                this.addStep(`查找导出 「${name}」 数据失败`, name, 'error', 1)
                return;
            }
            this.$xbcode.usePost('/app/xbDeveloper/admin/Index/export', {
                active: name,
                name: this.plugin.name,
            }).then(res => {
                if (res.status === 0) {
                    this.addStep(`导出 「${activeData.title}」 数据成功`, name)
                } else {
                    this.addStep(`导出 「${activeData.title}」 数据失败`, name, 'error')
                }
            }).catch(() => {
                this.addStep(`导出 「${activeData.title}」 数据失败`, name, 'error')
            }).finally(() => {
                const activeIndex = this.checkedTypes.active.findIndex((item) => item === name);
                if (activeIndex > -1) {
                    const nextName = this.checkedTypes.active[activeIndex + 1] ?? null;
                    if (nextName) {
                        const next = this.checkedTypes.list.find((item) => item.name === nextName);
                        setTimeout(() => {
                            this.addStep(`正在导出 「${next.title}」 ...`, 'detail', 1)
                            this.hanldExport(next?.name);
                        }, 2500);
                    } else {
                        this.addStep('插件数据导出完成...', 'success', 'success')
                        setTimeout(() => {
                            this.addStep('窗口将于3秒后，自动关闭...', 'success', 'success')
                        }, 2500);
                        setTimeout(() => {
                            this.$emit('close')
                        }, 5000);
                    }
                }
            })
        },
        hanldSubmit() {
            this.$emit('close')
            return;
            if (!this.checkedTypes.active.length) {
                this.$xbcode.useNotify('请至少选择一个导出类型', 'error', '温馨提示')
                return;
            }
            if (!this.plugin) {
                this.$xbcode.useNotify('插件数据参数错误', 'error', '温馨提示')
                return;
            }
            this.$xbcode.useConfirm('是否确定导出选择的数据类型？', '温馨提示', 'warning').then(() => {
                this.addStep('即将开始导出插件数据...', 'detail', 1)
                setTimeout(() => {
                    const name = this.checkedTypes.active[0];
                    const first = this.checkedTypes.list.find((item) => item.name === name);
                    this.addStep(`正在导出 「${first?.title}」 ...`, first?.name, 1)
                    this.hanldExport(first?.name);
                }, 1500);
            })
        },
        getDetail() {
            const name = this.$route.query.name;
            this.$xbcode.useGet('/app/xbDeveloper/admin/Index/detail', {
                name,
            }).then(res => {
                this.plugin = res.data ?? {};
            })
        },
        addStep(text, step, type = 'text', timeout = 0) {
            this.progress.step = step
            if (timeout > 0) {
                setTimeout(() => {
                    this.progress.list.push({
                        text,
                        type
                    })
                }, timeout * 1000);
            } else {
                this.progress.list.push({
                    text,
                    type
                })
            }
        },
    },
}
</script>

<style lang="scss" scoped>
.import-container {
    height: 100%;

    .body-container {
        height: 100%;

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

    .form-container {
        height: 100%;
        overflow-y: auto;
        padding: 20px;

        .tag-container {
            margin-bottom: 20px;

            .tag-content {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

        }
    }
}
</style>