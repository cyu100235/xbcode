<template>
    <div class="authorize">
        <div class="body">
            <div class="login-form">
                <div class="title">用户登录</div>
                <el-form :model="form" label-width="auto">
                    <el-form-item label="登录账号">
                        <el-input v-model="form.username" placeholder="请填写登录账号" />
                    </el-form-item>
                    <el-form-item label="登录密码">
                        <el-input v-model="form.password" type="password" placeholder="请填写登录密码" />
                    </el-form-item>
                    <div class="reg-pass">
                        <el-button type="primary" link>注册账号</el-button>
                        <el-button type="primary" link>忘记密码</el-button>
                    </div>
                    <div class="submit">
                        <el-button type="primary" @click="onSubmit">
                            立即登录
                        </el-button>
                    </div>
                </el-form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            form: {
                username: '',
                password: ''
            }
        }
    },
    methods: {
        onSubmit() {
            this.$usePost('backend/Server/login', this.form).then((res) => {
                this.$router.push({ path: '/' })
            })
        }
    }
}
</script>

<style lang="scss" scoped>
.authorize {
    height: 100%;
    padding: 15px;

    .body {
        height: 100%;
        background-color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        .login-form {
            width: 400px;
            padding: 20px;
            border: 1px solid #e1e1e1;
            border-radius: 6px;
            text-align: center;
            .title{
                padding: 20px 0;
                font-size: 18px;
            }
            .reg-pass{
                display: flex;
                justify-content: space-between;
            }
            .submit{
                display: flex;
                justify-content: center;
                padding-top: 20px;
            }
        }
    }
}
</style>