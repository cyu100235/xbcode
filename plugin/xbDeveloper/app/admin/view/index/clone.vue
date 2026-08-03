<template>
    <div class="import-container">
        <div class="body-container">
            <!-- 现实表单 -->
            <div class="form-container">
                <div class="tag-container">
                    <el-alert type="warning" :closable="false">
                        <div class="tag-content">
                            <div>注意：</div>
                            <div>初始化克隆仓库前</div>
                            <div>请确保您已经将SSH密钥配置到远程仓库或个人密钥</div>
                        </div>
                    </el-alert>
                </div>
                <el-form label-position="top" label-width="80px">
                    <el-form-item label="仓库地址">
                        <el-input placeholder="请填写SSH仓库地址" v-model="plugin.url" maxlength="255" showWordLimit @change="hanldUrl" />
                    </el-form-item>
                    <el-form-item label="插件名称">
                        <el-input placeholder="请填写插件名称" v-model="plugin.title" maxlength="10" showWordLimit />
                    </el-form-item>
                    <el-form-item label="插件标识">
                        <el-input placeholder="请填写插件标识" v-model="plugin.name" maxlength="15" showWordLimit />
                    </el-form-item>
                    <el-form-item label="插件描述">
                        <el-input placeholder="请填写插件描述" v-model="plugin.desc" maxlength="30" showWordLimit />
                    </el-form-item>
                    <el-form-item label="开发者名称">
                        <el-input placeholder="请填写开发者名称" v-model="plugin.author" maxlength="8" showWordLimit />
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="hanldSubmit">
                            确认初始化仓库
                        </el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            plugin: {
                url: '',
                title: '未命名插件',
                name: '',
                author: '积木云',
                desc: '一个全新的插件初始化',
            },
        }
    },
    methods: {
        hanldSubmit() {
            if (!this.plugin.url) {
                useVue.$useNotify('请填写仓库地址', 'error', '温馨提示')
                return
            }
            const loading = useVue.$useLoading('提交中...')
            useVue.$axios.post('/app/xbDeveloper/admin/Index/clone', this.plugin).then(res => {
                useVue.$useNotify(res?.msg ?? '操作成功', res?.code === 200 ? 'success' : 'error', '温馨提示')
                if (res?.code === 200) {
                    this.$emit('close')
                }
            }).finally(() => {
                loading.close()
            })
        },
        hanldUrl(value) {
            let url = value.replace(/git clone /, '')
            // 获取仓库名称
            let name = url.split('/').pop().replace(/\.git$/, '')
            // 替换xb开头字母为空，仅一次
            name = name.replace(/^xb/, '')
            // 首字母转小写
            name = name.charAt(0).toLowerCase() + name.slice(1)
            // 重新赋值
            this.plugin.name = name
            this.plugin.url = url
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
        padding: 20px;
        overflow-y: auto;

        .tag-container {
            margin-bottom: 20px;

            .tag-content {
                display: flex;
                flex-direction: column;
            }

        }
    }
}
</style>