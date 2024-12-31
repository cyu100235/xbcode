<template>
    <div class="buy-container">
        <div class="body" v-if="plugin.id">
            <div class="info-container">
                <div class="item">
                    <div class="label">插件名称</div>
                    <div class="value">{{ plugin.plugin_title }}</div>
                </div>
                <div class="item">
                    <div class="label">订单编号</div>
                    <div class="value">{{ plugin.order_no }}</div>
                </div>
                <div class="item">
                    <div class="label">订单金额</div>
                    <div class="value price">￥{{ plugin.total }}</div>
                </div>
                <div class="item" v-if="plugin.expire">
                    <div class="label">剩余时间</div>
                    <div class="value">
                        <el-countdown :value="plugin.expire" @finish="hanldFinish" />
                    </div>
                </div>
                <div class="item">
                    <div class="label">温馨提示</div>
                    <div class="value">请使用支付宝扫描二维码支付</div>
                </div>
            </div>
            <div class="content">
                <iframe ref="iframeRef" frameborder="0" class="iframe" />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            timer: null,
            plugin: {
                id: '',
                order_no: '',
                plugin_title: '',
                name: '',
                total: '',
                expire_time: '',
                expire: '',
                // 过期时间示例参数
                // expire_time: Date.now() + 1000 * 60 * 1,
                qrcode: '',
            }
        }
    },
    methods: {
        hanldFinish() {
            console.log('支付超时');
            this.$emit('close')
        },
        checked(order_no) {
            this.$axios.get('backend/Plugins/checked', {
                order_no
            }).then((res) => {
                if (res?.data?.checked) {
                    const type = res?.msg ? 'success' : 'error';
                    this.$useNotify(res?.msg ?? '网络请求错误', type, '温馨提示');
                    this.$emit('close')
                }
            }).catch(() => {
                this.$emit('close')
            })
        },
        order() {
            const _this = this;
            this.$axios.post('backend/Plugins/buy', {
                name: this.plugin.name
            }).then((res) => {
                if (res?.data?.is_pay) {
                    var plugin = res?.data;
                    // 转换毫秒级过期时间
                    plugin.expire = plugin.expire_time + 1000;
                    // 需要支付
                    this.plugin = plugin;
                    // 渲染二维码
                    const iframeContent = plugin.qrcode ?? '';
                    // 元素更新完成后再写入
                    this.$nextTick(() => {
                        this.$refs.iframeRef.contentWindow.document.open();
                        this.$refs.iframeRef.contentWindow.document.write(iframeContent);
                    });
                    this.checked(plugin.order_no);
                    this.timer = setInterval(() => {
                        _this.checked(plugin.order_no);
                    }, 2000);
                } else {
                    // 无需支付
                    const type = res?.msg ? 'success' : 'error';
                    this.$useNotify(res?.msg ?? '网络请求失败', type, '温馨提示');
                    this.$emit('close')
                }
            }).catch(err => {
                this.$emit('close')
            });
        }
    },
    mounted() {
        this.plugin.name = this.$route.query?.name;
        this.order();
    },
    unmounted() {
        if (this.timer) {
            clearInterval(this.timer);
        }
    },
}
</script>

<style lang="scss" scoped>
.buy-container {
    height: 100%;
    display: flex;
    align-items: center;

    .body {
        width: 400px;
        margin: 0 auto;

        .info-container {
            width: 300px;
            margin: 0 auto;

            .item {
                display: flex;
                margin-bottom: 10px;
                font-size: 14px;

                .label {
                    width: 60px;
                    margin-right: 10px;
                }

                .value {
                    flex: 1;
                    line-height: 20px;
                }

                .price {
                    color: red;
                    font-size: 16px;
                }
            }
        }

        .content {
            padding-top: 20px;

            .iframe {
                width: 220px;
                height: 220px;
                margin: 0 auto;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }
    }
}
</style>