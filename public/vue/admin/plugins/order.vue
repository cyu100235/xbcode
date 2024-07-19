<template>
    <div class="pages-container" v-if="detail">
        <div class="order-info">
            <div class="item">
                <span class="label">订单标题：</span>
                <span class="value">{{ detail?.title }}</span>
            </div>
            <div class="item">
                <span class="label">订单编号：</span>
                <span class="value">{{ detail?.order_no }}</span>
            </div>
            <div class="item">
                <span class="label">订单价格：</span>
                <span class="value price">￥{{ detail?.total }}</span>
            </div>
        </div>
        <div class="content">
            <div class="pay-type">
                <div class="pay-list">
                    <div class="item" :class="{ 'active': payType === item.name }" v-for="(item, index) in paylist"
                        :key="index" @click="hanldPayType(item?.name)">
                        <el-image class="logo" :src="item.logo" />
                    </div>
                </div>
                <div class="other-list">
                    <div class="item">
                        <el-button class="btn" type="primary" plain>
                            不支持七天无理由退款
                        </el-button>
                    </div>
                    <div class="item">
                        <el-button class="btn" type="primary" plain>
                            授权协议 & 授权说明
                        </el-button>
                    </div>
                </div>
            </div>
            <div class="pay-qrcode">
                <div class="logo-box">
                    <el-image class="logo" v-if="payQrcode" :src="payQrcode" />
                    <div class="text" v-else>请选择支付方式</div>
                </div>
                <div class="tip">
                    <AppIcons icon="FullScreen" :size="40" />
                    <div class="qrcode">
                        <div class="item">请使用支付宝扫一扫</div>
                        <div class="item">扫描二维码支付</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        name: String,
        version: String | Number,
    },
    data() {
        return {
            detail: null,
            payType: '',
            payQrcode: '',
            paylist: [
                // {
                //     title: '支付宝支付',
                //     name: 'alipay',
                //     logo: '/static/image/alipay.png',
                // },
                // {
                //     title: '微信支付',
                //     name: 'wechat',
                //     logo: '/static/image/wechat.png',
                // },
                {
                    title: '余额支付',
                    name: 'balance',
                    logo: '/static/image/cash.png',
                },
            ],
        }
    },
    mounted() {
        this.getData()
    },
    methods: {
        // 发起支付
        hanldPayType(type) {
            this.payType = type
            if (type.includes('balance')) {
                this.$useConfirm('是否确认余额支付购买？', '温馨提示', 'warning').then(() => {
                    this.unifiedOrder()
                }).catch(() => { 
                    this.payType = ''
                })
            } else {
                this.unifiedOrder()
            }
        },
        // 统一下单
        unifiedOrder() {
            const params = {
                order_no: this.detail?.order_no,
                pay_type: this.payType,
            }
            this.$http.useGet('admin/Plugins/unifiedOrder', params).then((res) => {
                if (res?.code !== 200) {
                    this.$emit("update:closeWin");
                    return
                }
                // 余额付款
                if (!res?.data?.is_pay && this.payType === 'balance') {
                    this.$emit("update:closeWin");
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                    this.$useNotify(res?.msg ?? '异常错误', 'success', '温馨提示')
                }
            }).catch((err) => {
                if (err?.code === 11000) {
                    this.$emit("update:closeWin");
                    this.$useRemote('/vue/admin/cloud/login', {}, {
                        title: '云服务登录',
                        customStyle: {
                            width: '420px',
                            height: '450px',
                        },
                    })
                }
            })
        },
        // 获取订单信息
        getData() {
            const params = {
                name: this.name,
                version: this.version
            }
            this.$http.useGet('admin/Plugins/order', params).then((res) => {
                if (res?.code !== 200) {
                    this.$emit("update:closeWin");
                    return
                }
                this.detail = res?.data ?? null
            }).catch((err) => {
                this.$emit("update:closeWin");
                if (err?.code === 11000) {
                    this.$useRemote('/vue/admin/cloud/login', {}, {
                        title: '云服务登录',
                        customStyle: {
                            width: '420px',
                            height: '450px',
                        },
                    })
                }
            })
        }
    },
}
</script>

<style lang="scss" scoped>
.pages-container {
    height: 100%;

    .order-info {
        display: flex;
        flex-direction: column;
        row-gap: 8px;
        padding: 20px;

        .item {
            font-size: 14px;

            .label {}

            .label {}

            .price {
                color: #f56c6c;
                font-weight: 700;
            }
        }
    }

    .content {
        display: flex;
        column-gap: 20px;
        padding: 20px;
        border-top: 1px solid #f1f1f1;

        .pay-type {
            flex: 1;

            .pay-list {
                display: flex;
                flex-direction: column;
                row-gap: 20px;

                .item {
                    height: 50px;
                    border-radius: 6px;
                    border: 2px solid #ddd;
                    padding: 6px;
                    display: flex;
                    align-items: center;
                    cursor: pointer;

                    &:hover {
                        border: 2px solid #409eff;
                    }
                }

                .active {
                    border: 2px solid #409eff;
                }
            }

            .other-list {
                display: flex;
                flex-direction: column;
                padding-top: 15px;

                .item {
                    padding: 6px 0;

                    .btn {
                        width: 100%;
                    }
                }
            }
        }

        .pay-qrcode {
            .logo-box {
                width: 300px;
                height: 300px;
                position: relative;

                .logo {
                    width: 100%;
                }

                .text {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: #888;
                    color: #fff;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    font-size: 18px;
                    border-radius: 4px;
                    user-select: none;
                }
            }

            .tip {
                display: flex;
                align-items: center;
                column-gap: 10px;
                margin-top: 1px;
                background-color: #409eff;
                padding: 10px 20px;
                border-radius: 4px;
                color: #fff;
                user-select: none;

                .qrcode {
                    display: flex;
                    flex-direction: column;
                    row-gap: 5px;

                    .item {}
                }
            }
        }
    }
}
</style>