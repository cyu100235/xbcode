<template>
    <div class="xbcode-container">
        <!-- 系统公告 -->
        <div class="notice">
            <el-carousel direction="vertical" height="50px" v-if="notice.length">
                <el-carousel-item v-for="(item, index) in notice" :key="index" @click="hanldNotice(item)">
                    <div class="notice-item">
                        <xbIcons icon="SoundOutlined" size="16" />
                        <span>{{ item.title }}</span>
                    </div>
                </el-carousel-item>
            </el-carousel>
        </div>
        <!-- 工具栏 -->
        <div v-if="toolbar.length" class="header-tools">
            <div class="item" v-for="(item, index) in toolbar" :key="index" @click="item.hanlder">
                <xbIcons class="icon" :icon="item.icon" :size="17" :color="item.isUpdate ? '#ff0000' : '#555'" />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            // 是否全屏
            fullscreen: false,
            // 系统公告
            notice: [],
            // 工具栏
            toolbar: [
                {
                    title: '刷新页面',
                    name: 'refresh',
                    icon: 'Refresh',
                    hanlder: () => {
                        window.location.reload()
                    },
                },
                {
                    title: '官网首页',
                    name: 'home',
                    icon: 'Monitor',
                    hanlder: () => {
                        window.open('/')
                    },
                },
                {
                    title: '全屏缩放',
                    name: 'zoom',
                    icon: 'ExpandOutlined',
                    hanlder: () => {
                        const element = document.documentElement
                        // 如果是全屏状态
                        if (this.fullscreen) {
                            // 如果浏览器有这个Function
                            if (document.exitFullscreen) {
                                document.exitFullscreen()
                            } else if (document.webkitCancelFullScreen) {
                                document.webkitCancelFullScreen()
                            } else if (document.mozCancelFullScreen) {
                                document.mozCancelFullScreen()
                            } else if (document.msExitFullscreen) {
                                document.msExitFullscreen()
                            }
                        } else {
                            // 如果浏览器有这个Function
                            if (element.requestFullscreen) {
                                element.requestFullscreen()
                            } else if (element.webkitRequestFullScreen) {
                                element.webkitRequestFullScreen()
                            } else if (element.mozRequestFullScreen) {
                                element.mozRequestFullScreen()
                            } else if (element.msRequestFullscreen) {
                                element.msRequestFullscreen()
                            }
                        }
                        // 判断全屏状态的变量
                        this.fullscreen = !this.fullscreen
                    },
                },
            ]
        }
    },
    mounted() {
        this.getNoticeList()
    },
    methods: {
        hanldNotice(row) {
            this.$router.push({
                path: '/admin/notice/detail',
                query: {
                    id: row.id
                }
            })
        },
        getNoticeList() {
            this.$useGet('admin/Notice/index').then(res => {
                this.notice = res.data
            });
        }
    },
}
</script>

<style lang="scss" scoped>
.xbcode-container {
    height: 100%;
    display: flex;

    .notice {
        flex: 1;
        padding: 0 10px;

        .notice-item {
            height: 100%;
            display: flex;
            gap: 4px;
            align-items: center;
            font-size: 14px;
            color: #555;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;

            &:hover {
                color: #F4874B;
            }
        }
    }

    .header-tools {
        height: 100%;
        display: flex;
        justify-content: flex-end;
        align-items: center;

        .item {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            user-select: none;
            cursor: pointer;
            padding: 0 10px;

            &:hover {
                .icon {
                    -webkit-animation: logo-animation .3s ease-in-out;
                    animation: logo-animation .3s ease-in-out;
                }
            }
        }
    }
}

@keyframes logo-animation {
    0% {
        -webkit-transform: scale(0);
        transform: scale(0)
    }

    80% {
        -webkit-transform: scale(1.2);
        transform: scale(1.2)
    }

    to {
        -webkit-transform: scale(1);
        transform: scale(1)
    }
}
</style>