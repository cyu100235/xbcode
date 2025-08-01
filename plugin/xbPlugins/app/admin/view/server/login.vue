<template>
    <div class="qrcode-container">
        <div class="qrcode">
            <img :src="qrcode" alt="" class="qrcode" v-if="qrcode" />
            <div class="mask" v-else>正在获取登录二维码...</div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        loginCallback: {
            type: Function,
            default: () => {}
        }
    },
    data() {
        return {
            code: '',
            qrcode: '',
            timer: null
        }
    },
    mounted() {        
        this.getQrcode()
    },
    unmounted() {
        if (this.timer) {
            clearTimeout(this.timer)
        }
    },
    methods: {
        checkedLogin() {
            useVue.$axios.get('app/xbPlugins/admin/Server/qrcode', {
                code: this.code
            }).then(res => {
                if (res?.data?.state === 1) {
                    useVue.$useNotify('登录成功', 'success', '温馨提示')
                    // 执行登录成功回调
                    this.loginCallback()
                    // 移除定时器
                    if (this.timer) {
                        clearTimeout(this.timer)
                    }
                    // 登录成功
                    this.$emit('close')
                } else if (res?.code === 202) {
                    // 二维码过期
                    this.getQrcode()
                }else {
                    this.timer = setTimeout(() => {
                        this.checkedLogin()
                    }, 2000)
                }
            })
        },
        getQrcode() {
            useVue.$axios.get('app/xbPlugins/admin/Server/qrcode').then(res => {
                this.code = res?.data?.code ?? ''
                this.qrcode = res?.data?.url ?? ''
                this.timer = setTimeout(() => {
                    this.checkedLogin()
                }, 2000);
            })
        }
    }
}
</script>

<style lang="scss" scoped>
.qrcode-container {
    display: flex;
    gap: 20px;
    flex-direction: column;
    justify-content: center;
    align-items: center;

    .qrcode {
        width: 380px;
        height: 380px;

        img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .mask {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(#000000, 0.6);
            color: #fff;
            font-size: 20px;
        }
    }
}
</style>