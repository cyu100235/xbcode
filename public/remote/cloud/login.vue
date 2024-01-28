<template>
    <div class="form-page">
        <div class="form-container">
            <el-form :model="form" @submit.native.prevent="onSubmit" label-position="top">
                <el-form-item label="登录账号">
                    <el-input v-model="form.username" placeholder="请输入手机号码" />
                </el-form-item>
                <el-form-item label="登录密码">
                    <el-input type="password" v-model="form.password" placeholder="请输入登录密码" />
                </el-form-item>
                <div class="action-btn">
                    <a :href="system_info?.link_url?.register ?? ''" target="_blank">注册账号</a>
                    <a :href="system_info?.link_url?.forgot ?? ''" target="_blank">忘记密码</a>
                </div>
                <div class="submit-button">
                    <el-button type="primary" class="cls-button" @click="onSubmit">
                        立即登录
                    </el-button>
                </div>
            </el-form>
        </div>
    </div>
</template>
  
<script>
export default {
    data() {
        return {
            form: {
                username: "",
                password: "",
            },
            system_info: {},
        };
    },
    props: {
        url: String,
    },
    mounted() {
        this.$useKeyCodeEvent(() => this.onSubmit())
    },
    methods: {
        openWin(path) {
            this.$emit("update:openWin", path);
        },
        onSubmit() {
            var _this = this;
            _this.$http.usePost("admin/Cloud/login", _this.form)
                .then((res) => {
                    _this.$emit("update:closeWin");
                    _this.$useNotify(res?.msg || "登录成功", 'success', '温馨提示')
                })
        },
    },
};
</script>
  
<style lang="scss" scoped>
.form-page {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;

    .form-container {
        margin: 0 auto;
        width: 100%;
        padding: 0 30px;

        .captcha {
            width: auto;
            height: 32px;
            cursor: pointer;
        }

        .action-btn {
            display: flex;
            justify-content: space-between;
        }

        .submit-button {
            margin-top: 20px;

            .cls-button {
                width: 100%;
            }
        }
    }
}

.vcode-button {
    cursor: pointer;
}
</style>
  