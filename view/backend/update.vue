<template>
    <div class="authorize">
        <div class="body">
            <div class="update-version-container">
                <div class="version-info">
                    <!-- 有更新 -->
                    <div class="update-container" v-if="updateInfo.status">
                        <div class="title">
                            <div class="text">
                                当前版本
                                {{ updateInfo.detail.local_version_name }}
                            </div>
                            <div class="checked" @click="checked()">
                                <xbIcons icon="InfoFilled" />
                                检查版本更新
                            </div>
                        </div>
                        <div class="content">
                            <div class="main-tabs">
                                <div class="item" :class="{ active: stepData.step === item?.step }"
                                    v-for="(item, index) in stepData.list" :key="index">
                                    <div class="step">{{ index + 1 }}</div>
                                    <div class="text">{{ item.title }}</div>
                                </div>
                            </div>
                            <!-- 更新中 -->
                            <div class="update-desc-container" v-if="stepData.lock">
                                <div class="update-desc-title">
                                    <div class="next-title">更新中，请勿刷新或离开当前页面...</div>
                                </div>
                                <div class="update-ing">
                                    <AppIcons class="update-loading" icon="Refresh" />
                                    <span class="update-loading-text">{{ stepData?.updateText }}</span>
                                </div>
                            </div>
                            <!-- 准备更新 -->
                            <div class="update-desc-container" v-else>
                                <div class="update-desc-title">
                                    <div class="update-desc-version">
                                        发现版本更新，版本
                                        {{ updateInfo.detail.new_version_name }}
                                    </div>
                                    <div class="update-desc-time">
                                        发布时间：{{ updateInfo.detail.create_at }}
                                    </div>
                                </div>
                                <pre class="update-desc">{{ updateInfo.detail.content }}</pre>
                                <div class="update-buttons">
                                    <button class="update-btn submit-btn" @click="hanldUpdate">
                                        立即更新
                                    </button>
                                </div>
                            </div>
                            <!-- 温馨提示 -->
                            <div class="update-tip">
                                <el-alert title="温馨提示" type="error" :closable="false">
                                    <div class="update-line-content">
                                        <div>1、无论更新成功与否，都会在更新前备份代码与数据库</div>
                                        <div>2、备份的代码与数据库会在站点根目录下的backup目录下</div>
                                        <div>3、切记，backup目录下的备份文件，相同版本备份文件会覆盖</div>
                                        <div>4、更新过程中，会出现更新中的提示，不要刷新页面或离开当前页面</div>
                                        <div>5、更新前，请确保站点根目录下的.env文件里的APP_DEBUG为false</div>
                                    </div>
                                </el-alert>
                            </div>
                        </div>
                    </div>
                    <!-- 无更新 -->
                    <div class="xbcode-empty" v-else-if="updateInfo.is_new">
                        <el-image class="logo" src="/static/image/update.png"></el-image>
                        <div class="content">
                            当前已经是最新的，版本：{{ updateInfo.detail.new_version_name }}
                        </div>
                        <div class="updated">
                            <el-button type="primary" @click="checked()">
                                检查更新
                            </el-button>
                        </div>
                    </div>
                    <!-- 检测更新 -->
                    <div class="xbcode-checked" v-else>
                        <xbIcons icon="Refresh" size="20" class="is-loading" />
                        <div class="text">正在检测更新...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            // 是否有版本更新
            updateInfo: {
                status: false,
                detail: {
                    // 发布时间
                    create_at: '',
                    // 当前版本
                    version: '--',
                    // 新版本
                    new_version: '--',
                    // 更新内容
                    content: '',
                },
            },
            // 更新步骤数据
            stepData: {
                step: 'preparation',
                lock: false,
                updateText: '',
                list: [
                    {
                        title: '准备更新',
                        step: 'preparation',
                    },
                    {
                        title: '下载更新包',
                        step: 'download',
                    },
                    {
                        title: '备份代码',
                        step: 'backupCode',
                    },
                    {
                        title: '备份数据库',
                        step: 'backupSql',
                    },
                    {
                        title: '解压更新包',
                        step: 'unzip',
                    },
                    {
                        title: '更新数据',
                        step: 'updateData',
                    },
                    {
                        title: '重启服务',
                        step: 'restart',
                    },
                    {
                        title: '更新成功',
                        step: 'success',
                    },
                ],
            },
        }
    },
    mounted() {
        this.getVersion();
    },
    methods: {
        // 更新进行中
        hanldUpdateIng(step) {
            const _this = this;
            // 获取目标版本名称
            const newVersionName = _this.updateInfo.detail.new_version_name;
            // 获取目标版本编号
            const newVersion = _this.updateInfo.detail.new_version;
            // 设置开始更新与文字
            const item = _this.stepData.list.find(item => item.step === step);
            _this.stepData.updateText = `正在${item.title}...`;
            // 设置当前步骤
            _this.stepData.step = step
            // 发送更新请求
            _this.$usePost('backend/Server/update', {
                step: step,
                version_name: newVersionName,
                version: newVersion
            }).then((res) => {
                if (res?.data?.next === '') {
                    _this.stepData.step = 'success';
                    _this.stepData.updateText = res.msg;
                    setTimeout(() => {
                        _this.$router.push({ path: '/' });
                    }, 2500);
                } else if (res?.data?.next) {
                    _this.hanldUpdateIng(res.data.next);
                }
            }).catch((err) => {
                if (err?.response?.status === 502 && _this.stepData.step === 'restart') {
                    setTimeout(() => {
                        _this.hanldUpdateIng('success');
                    }, 2000);
                    return;
                } else if (err?.message?.includes('timeout')) {
                    _this.$useNotify("更新失败，网络超时", 'error', '温馨提示', {
                        'onClose': () => {
                            window.location.reload();
                        }
                    })
                    return;
                }
                _this.stepData.step = 'preparation';
                _this.stepData.lock = false;
            });
        },
        // 点击更新
        hanldUpdate() {
            this.$useConfirm('是否确定开始更新？', '温馨提示', 'success').then(() => {
                // 锁定更新
                this.stepData.lock = true;
                this.hanldUpdateIng('download');
            })
        },
        // 清除更新缓存
        checked() {
            const loading = this.$useLoading('正在检查更新');
            this.$usePost('backend/Server/checked').then((res) => {
                this.$useNotify(res?.msg ?? '网络错误', 'success', '温馨提示');
                if (res?.data?.status) {
                    this.getVersion();
                }
            }).finally(() => {
                loading.close();
            });
        },
        // 获取版本信息
        getVersion() {
            this.$usePost('backend/Server/version').then((res) => {
                const data = res?.data ?? this.updateInfo;
                this.updateInfo = data;
            });
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

        .update-version-container {
            height: 100%;
            background: #fff;

            .version-info {
                height: 100%;

                .update-container {
                    height: 100%;
                    background: #fff;
                    display: flex;
                    flex-direction: column;

                    .title {
                        padding: 20px 30px;
                        display: flex;
                        align-items: center;

                        .text {
                            font-size: 18px;
                        }

                        .checked {
                            font-size: 14px;
                            padding-left: 10px;
                            cursor: pointer;
                            color: #ff5722;
                            display: flex;
                            align-items: center;
                            gap: 4px;
                            user-select: none;

                            &:hover {
                                color: #006EFF;
                            }
                        }
                    }

                    .content {
                        flex: 1;
                        padding: 0 30px;
                        overflow-y: auto;
                        overflow-x: hidden;

                        .main-tabs {
                            display: flex;
                            border-bottom: 1px solid #e5e5e5;

                            .item {
                                width: 130px;
                                display: flex;
                                flex-direction: column;
                                justify-content: center;
                                align-items: center;
                                gap: 10px;
                                padding: 15px 0;
                                user-select: none;

                                .step {
                                    width: 40px;
                                    height: 40px;
                                    background: #ccc;
                                    border-radius: 50%;
                                    display: flex;
                                    justify-content: center;
                                    align-items: center;
                                    color: #fff;
                                    font-weight: 700;
                                }

                                .text {
                                    font-weight: 700;
                                    font-size: 16px;
                                }
                            }

                            .active {
                                border-bottom: 5px solid #006EFF;

                                .step {
                                    background: #006EFF;
                                }
                            }
                        }

                        .update-desc-container {
                            padding: 20px 0;

                            .update-desc-title {
                                font-size: 20px;
                                font-weight: 700;
                                display: flex;
                                justify-content: space-between;

                                // .update-desc-version {}

                                // .update-desc-time {}

                                .next-title {
                                    color: red;
                                }
                            }

                            .update-ing {
                                display: flex;
                                align-items: center;
                                font-size: 14px;
                                line-height: 26px;
                                color: #70767E;
                                white-space: pre-wrap;
                                word-break: break-word;
                                padding: 20px 0;
                                margin: 0;

                                .update-loading {
                                    animation: update-ing 1s linear infinite;
                                }

                                .update-loading-text {
                                    padding-left: 5px;
                                }
                            }

                            .update-desc {
                                height: 230px;
                                font-size: 14px;
                                line-height: 26px;
                                color: #70767E;
                                white-space: pre-wrap;
                                word-break: break-word;
                                padding: 10px 0;
                                margin: 10px 0;
                                overflow-x: hidden;
                                overflow-y: auto;
                                border-bottom: 1px solid #e5e5e5;
                            }

                            .update-buttons {
                                display: flex;
                                gap: 30px;

                                .update-btn {
                                    width: 150px;
                                    height: 40px;
                                    border-radius: 4px;
                                    border: 0;
                                    cursor: pointer;
                                }

                                .submit-btn {
                                    color: #fff;
                                    background: #016EFF;
                                    transition: all 0.1s;

                                    &:hover {
                                        background: #61a6ff;
                                    }
                                }

                                .rest-btn {
                                    background: #fff;
                                    color: #016EFF;
                                    border: 1px solid #e1e1e1;
                                    transition: all 0.1s;

                                    &:hover {
                                        background: #f9f9f9;
                                    }
                                }
                            }
                        }

                        .update-tip {
                            margin-top: 10px;
                        }
                    }
                }

                .xbcode-checked {
                    height: 100%;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    background-color: #fff;

                    .text {
                        padding-top: 6px;
                        font-size: 14px;
                    }
                }

                .xbcode-empty {
                    height: 100%;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;

                    .logo {
                        width: 300px;
                        height: 200px;
                    }

                    .content {
                        font-size: 14px;
                    }

                    .updated {
                        padding-top: 15px;
                    }
                }
            }
        }
    }
}

.update-line-content {
    line-height: 36px;
    font-size: 16px;
}

@keyframes update-ing {
    from {
        -webkit-animation: rotate(0deg);
        transform: rotate(0deg);
    }

    to {
        -webkit-animation: rotate(360deg);
        transform: rotate(360deg);
    }
}
</style>