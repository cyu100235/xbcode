<template>
    <div class="pages-container">
        <div class="form-container" v-if="detail">
            <ElAlert type="error" title="温馨提示" :closable="false">
                <div>
                    版本发布后，将不可编辑！请认真填写后，请认真填写内容后发布
                </div>
            </ElAlert>
            <el-form :model="formData" label-position="top" class="form-content">
                <el-form-item label="应用名称">
                    <el-input :value="detail?.title" disabled />
                </el-form-item>
                <el-form-item label="应用ID">
                    <el-input :value="detail?.name" disabled />
                </el-form-item>
                <el-form-item label="版本名称" prop="version_name">
                    <el-input v-model="formData.version_name" placeholder="请输入版本名称" />
                    <div class="prompt">
                        <div class="title">
                            当前最新版本：
                            {{ detail?.new_version_name }}
                            （{{ detail?.new_version }}）
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="适用框架" prop="saas_version_id">
                    <el-select v-model="formData.saas_version_id" class="form-select" placeholder="请选择框架使用版本">
                        <el-option v-for="(item, index) in saasVersionOptions" :key="index" :label="item.version_name"
                            :value="item.id">
                            <div class="flex items-center justify-between text-[12px]">
                                <span>版本名：{{ item.version_name }}</span>
                                <span class="text-cool-gray-500">版本号：{{ item.version }}</span>
                            </div>
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="升级模式" prop="is_span">
                    <el-radio-group v-model="formData.is_span">
                        <el-radio label="10" border>
                            正常升级
                        </el-radio>
                        <el-radio label="20" border>
                            禁止跨版本
                        </el-radio>
                    </el-radio-group>
                    <div class="prompt">
                        <div class="title">
                            注意：
                        </div>
                        <div class="desc">
                            1. 当有SQL更新时，请选择“禁止跨版本”
                        </div>
                        <div class="desc">
                            2. 允许跨版本会将跨度的所有版本合并成升级包
                        </div>
                    </div>
                </el-form-item>
                <el-form-item label="更新日志" prop="content">
                    <el-input v-model="formData.content" type="textarea" rows="8" resize="none" placeholder="请输入更新日志" />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="onSubmit()">
                        提交审核
                    </el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        app_name: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            detail: null,
            saasVersionOptions:[],
            formData: {
                app_id: '',
                version_name: '',
                saas_version_id: '',
                content: '',
                is_span: '10',
            },
        }
    },
    async mounted() {
        await this.getDetail();
        await this.getFrameVersion();
    },
    methods: {
        // 提交审核
        onSubmit() {
            const params = {
                ...this.formData,
                app_id: this.detail.id,
            }
            this.$http.usePost('admin/developer/publish', params).then(res => {
                this.$useNotify(res?.msg || "网络错误", 'success', '温馨提示')
                this.$emit("update:closeWin");
            })
        },
        // 获取框架版本
        getFrameVersion() {
            this.$http.useGet('admin/developer/getFrameVersion').then(res => {
                this.saasVersionOptions = res?.data ?? []
            })
        },
        // 获取详情
        getDetail() {
            const params = {
                app_name: this.app_name
            }
            this.$http.useGet('admin/apps/detail', params).then(res => {
                this.detail = res?.data ?? {}
            })
        }
    },
}
</script>

<style lang="scss" scoped>
.pages-container {
    .form-container {
        width: 500px;
        margin: 0 auto;

        .form-content {
            padding: 10px 0;
            .form-select{
                width: 100%;
            }

            .prompt {
                width: 100%;

                .title {
                    font-size: 14px;
                    font-weight: bold;
                }

                .desc {
                    font-size: 12px;
                    color: red;
                }
            }
        }
    }
}
</style>