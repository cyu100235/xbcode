<template>
    <div class="plugin-container">
        <div class="title">插件管理</div>
        <div class="content" v-if="datalist.length">
            <div class="item" v-for="(item,index) in datalist" :key="index" @click="detail(item?.name)">
                <div class="logo-container">
                    <el-image class="logo" src="https://fuss10.elemecdn.com/e/5d/4a731a90594a4af544c0c25941171jpeg.jpeg" />
                </div>
                <div class="money">
                    {{ parseFloat(item?.money) ? `￥${item?.money}` : '免费'}}
                </div>
            </div>
        </div>
        <div class="empty-container" v-else>
            <el-empty description="该应用暂无插件" />
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            datalist:[]
        }
    },
    async mounted() {
        await this.getList()
    },
    methods: {
        // 插件详情
        detail(name) {
            const params = {
                app_name: name
            }
            this.$useRemote('remote/plugin/detail', params, {
                title: '插件详情',
                customStyle: {
                    width: '500px',
                    height: '80vh',
                },
                beforeClose: (value, state, done) => {
                    this.getList()
                    done()
                }
            })
        },
        // 获取插件列表
        getList() {
            this.$http.useGet(`${this.$moduleName}/Plugins/index`).then(res => {
                this.datalist = res?.data ?? []
            })
        },
    },
}
</script>

<style lang="scss">
.plugin-container {
    height: 100%;
    background-color: #fff;
    padding-bottom: 20px;

    .title {
        height:45px;
        border-bottom: 1px solid #e8e8e8;
        font-size: 16px;
        display: flex;
        align-items: center;
        padding: 0 20px;
    }

    .content {
        display: grid;
        grid-template-columns: repeat(24, 1fr);
        overflow-y: auto;
        overflow-x: hidden;
        .item {
            grid-column: span 3;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-top:15px;
            cursor: pointer;
            .logo-container {
                display: flex;
                justify-content: center;
                .logo {
                    width: 80px;
                    height: 80px;
                    border-radius: 5px;
                }
            }

            .money {
                display: flex;
                justify-content: center;
                align-items: center;
                padding-top: 5px;
                color: red;
                font-size: 14px;
            }
        }
    }
}</style>