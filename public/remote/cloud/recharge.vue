<template>
    <div class="pages-container" v-if="user">
        <!-- 充值界面 -->
        <div class="recharge-container" v-if="!qrcode">
            <div class="account">
                <div class="label">账户余额：</div>
                <div class="value">￥{{ user?.balance }}</div>
            </div>
            <div class="pay-container">
                <div class="label">充值金额：</div>
                <div class="recharge">
                    <el-input v-model="formData.money" placeholder="请输入充值金额" class="input" />
                </div>
            </div>
            <div class="type-container">
                <div class="label">支付方式：</div>
                <div class="pay">
                    <el-radio-group v-model="formData.pay_type">
                        <el-radio label="wxpay" border>
                            微信支付
                        </el-radio>
                        <!-- <el-radio label="alipay" border>
                            支付宝支付
                        </el-radio> -->
                    </el-radio-group>
                </div>
            </div>
            <div class="recharge-btn">
                <div class="submit" @click="hanldPay">
                    立即充值
                </div>
            </div>
        </div>
        <!-- 二维码界面 -->
        <div class="qrcode-container" v-else>
            <div class="title">充值金额：￥{{ formData.money }}</div>
            <el-image :src="qrcode" class="qrcode" />
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            user: null,
            qrcode:'',
            formData: {
                money: '',
                pay_type: 'wxpay',
            }
        }
    },
    async mounted() {
        await this.getUser().then(() => {
        })
    },
    methods: {
        // 获取充值二维码
        getQrcode(order_no) {
            const params = {
                order_no,
                pay_type: this.formData.pay_type,
            }
            this.$http.usePost('', params).then((res) => {
                console.log(res);
            })
        },
        // 提交充值
        hanldPay() {
            this.$http.usePost('admin/Cloud/recharge', this.formData).then((res) => {
                const order_no = res?.data?.order_no ?? ''
                if (!order_no) {
                    this.$useNotify(res?.msg || "网络错误", 'error', '温馨提示')
                    return
                }
                this.qrcode = `/admin/Cloud/getRechargeQrcode?order_no=${order_no}&pay_type=${this.formData.pay_type}`
            })
        },
        // 获取用户信息
        async getUser() {
            return await this.$http.useGet('admin/Cloud/user').then((res) => {
                if (res?.data?.code === 12000) {
                    this.$emit("update:closeWin");
                    this.$emit("update:openWin", 'remote/cloud/login');
                    return
                }
                this.user = res?.data ?? {}
                return res
            }).catch((res) => {
                if (res?.code === 12000) {
                    this.$emit("update:closeWin");
                    this.$emit("update:openWin", 'remote/cloud/login');
                }
            })
        },
    },
}
</script>

<style lang="scss" scoped>
.pages-container {
    padding-top: 100px;
    .recharge-container {
        width: 400px;
        margin: 0 auto;
        background-color: #f5f5f5;
        border-radius: 6px;
        padding: 40px 20px;

        .account {
            display: flex;
            font-size: 14px;

            .label {}

            .value {}
        }

        .pay-container {
            display: flex;
            margin-top: 30px;

            .label {
                display: flex;
                align-items: center;
            }

            .recharge {
                .input{
                    width: 100%;
                }
            }
        }

        .type-container {
            margin-top: 30px;
            display: flex;

            .label {
                display: flex;
                align-items: center;
            }

            .pay {}
        }

        .recharge-btn {
            display: flex;
            justify-content: center;
            margin-top: 30px;

            .submit {
                padding: 10px 50px;
                background-color: var(--el-color-warning);
                color: #fff;
                border-radius: 6px;
                cursor: pointer;

                &:hover {
                    background-color: var(--el-color-warning-light-3);
                }
            }
        }
    }
    .qrcode-container{
        width: 400px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        .title{
            font-size: 16px;
            padding: 10px 0;
        }
        .qrcode{
            width: 300px;
            height: 300px;
        }
    }
}
</style>