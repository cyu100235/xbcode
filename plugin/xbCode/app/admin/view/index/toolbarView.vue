<template>
    <div class="xb-todo-toolbar">
        <div class="header-tools">
            <!-- 首页 -->
            <div class="item" @click="hanldHome()">
                <XbIcons class="icon" icon="Monitor" size="20" color="#555" />
            </div>
            <!-- 待办事项：数据由各插件在 config/xbcode.php 的 todo 配置中提供；无任何待办页面权限时不展示 -->
            <el-popover v-if="todoVisible" placement="bottom-end" :width="300" trigger="hover" :show-after="150" :enterable="true"
                popper-class="xb-todo-popper" @after-enter="getTodo(false)">
                <template #reference>
                    <div class="item">
                        <div class="todo-icon-wrap">
                            <XbIcons class="icon" icon="BellOutlined" size="20" color="#555" />
                            <span class="todo-badge" v-if="todoCount > 0">
                                {{ todoCount > 99 ? '99+' : todoCount }}
                            </span>
                        </div>
                    </div>
                </template>
                <div class="todo-panel">
                    <div class="todo-panel-header">
                        <span class="todo-panel-title">待办事项</span>
                        <span class="todo-panel-total">共 {{ todoCount }} 项</span>
                    </div>
                    <div class="todo-panel-body">
                        <div class="todo-item" v-for="(item, index) in todoList" :key="index" @click="handleJump(item)">
                            <div class="todo-item-icon" :class="item.urgent ? 'is-urgent' : 'is-notice'">
                                <XbIcons :icon="item.icon || 'ScheduleOutlined'" size="16"
                                    :color="item.urgent ? '#f53f3f' : '#2468f2'" />
                            </div>
                            <div class="todo-item-info">
                                <div class="todo-item-title">{{ item.title }}</div>
                                <div class="todo-item-desc" v-if="item.desc">{{ item.desc }}</div>
                            </div>
                            <div class="todo-item-count" :class="item.urgent ? 'is-urgent' : 'is-notice'">
                                {{ item.count }}
                            </div>
                        </div>
                        <div class="todo-empty" v-if="!todoList.length">
                            {{ loading ? '加载中...' : '暂无待办事项' }}
                        </div>
                    </div>
                </div>
            </el-popover>
        </div>
        <!-- 待办提醒弹窗：紧急待办与新增待办共用，垂直水平居中 -->
        <el-dialog v-model="reminderVisible" width="560px" align-center append-to-body :show-close="false"
            :close-on-click-modal="false" :close-on-press-escape="false"
            :class="['xb-reminder-dialog', reminderLevel === 'urgent' ? 'is-urgent' : 'is-notice']">
            <div class="reminder-panel">
                <div class="reminder-head">
                    <div class="reminder-ring">
                        <XbIcons :icon="reminderLevel === 'urgent' ? 'AlertFilled' : 'NotificationFilled'" size="26"
                            color="#fff" />
                    </div>
                    <div class="reminder-head-text">
                        <div class="reminder-title">{{ reminderTitle }}</div>
                        <div class="reminder-subtitle">{{ reminderSubtitle }}</div>
                    </div>
                    <div class="reminder-amount" v-if="reminderList.length">
                        <span class="reminder-amount-num">{{ reminderTotal }}</span>
                        <span class="reminder-amount-unit">项待办</span>
                    </div>
                </div>
                <div class="reminder-body">
                    <div class="reminder-list">
                        <div class="reminder-item" v-for="(item, index) in reminderList" :key="index"
                            @click="handleReminderJump(item)">
                            <div class="reminder-item-icon" :class="item.urgent ? 'is-urgent' : 'is-notice'">
                                <XbIcons :icon="item.icon || 'ScheduleOutlined'" size="20"
                                    :color="item.urgent ? '#f53f3f' : '#2468f2'" />
                            </div>
                            <div class="reminder-item-info">
                                <div class="reminder-item-title">{{ item.title }}</div>
                                <div class="reminder-item-desc" v-if="item.desc">{{ item.desc }}</div>
                            </div>
                            <span class="reminder-item-count" :class="item.urgent ? 'is-urgent' : 'is-notice'">
                                {{ item.count }}
                            </span>
                            <XbIcons class="reminder-item-arrow" icon="RightOutlined" size="14" color="#c9cdd4" />
                        </div>
                        <div class="reminder-empty" v-if="!reminderList.length">暂无待办事项</div>
                    </div>
                </div>
                <div class="reminder-footer">
                    <el-checkbox v-model="dontRemindToday" class="reminder-check">今日不再提醒</el-checkbox>
                    <el-button type="primary" class="reminder-btn" @click="handleReminderClose">我知道了</el-button>
                </div>
            </div>
        </el-dialog>
    </div>
</template>

<script>
// 紧急提醒音频地址，文件放到 plugin/xbCode/public/static/audio/urgent.mp3 即可生效
const URGENT_AUDIO = '/app/xbCode/static/audio/urgent.mp3'
// 待办数量轮询间隔（毫秒）
const TODO_POLL_INTERVAL = 10000

export default {
    data() {
        return {
            // 是否展示待办铃铛并轮询：由接口返回的 enabled 决定，无任何待办页面权限时为 false
            todoVisible: false,
            // 待办事项列表
            todoList: [],
            // 待办事项总数
            todoCount: 0,
            // 是否正在加载
            loading: false,
            // 紧急待办列表
            urgentList: [],
            // 音频对象
            audio: null,
            // 音频是否不可用（文件缺失或解码失败）
            audioBroken: false,
            // 是否已绑定用户手势（自动播放被拦截时等待首次交互再播放）
            gestureBound: false,
            // 手势补播的监听函数
            gestureHandler: null,
            // 是否需要播放提醒音（播放被拦截时保持为 true，等用户交互补播）
            audioPending: false,
            // 轮询定时器
            pollTimer: null,
            // 是否显示待办提醒弹窗
            reminderVisible: false,
            // 弹窗来源：urgent 紧急待办检查 / notice 数量新增
            reminderMode: 'urgent',
            // 新增待办模式下本次提醒涉及的待办
            noticeList: [],
            // 提醒前的本地待办数量
            noticeFrom: 0,
            // 触发提醒时的待办数量
            noticeTo: 0,
            // 本地已确认的待办数量基准，-1 表示尚未初始化
            localCount: -1,
            // 本地已确认的待办条目快照，用于比对新增条目
            localList: [],
            // 是否已勾选「今日不再提醒」
            dontRemindToday: false,
        }
    },
    computed: {
        // 当前登录账号，用于区分「今日不再提醒」的存储键
        adminName() {
            const app = this.$xbcode && this.$xbcode.userApp
            return (app && app.userInfo && app.userInfo.username) || 'admin'
        },
        // 本次会话已提醒过的存储键
        sessionKey() {
            return `xb_todo_urgent_session_${this.adminName}`
        },
        // 本地待办数量记录的存储键（刷新后仍可继续比对）
        localCountKey() {
            return `xb_todo_local_count_${this.adminName}`
        },
        // 弹窗内展示的待办列表
        reminderList() {
            return this.reminderMode === 'urgent' ? this.urgentList : this.noticeList
        },
        // 弹窗配色级别：列表含紧急事项为 urgent（红色），否则 notice（蓝色）
        reminderLevel() {
            return (this.reminderList || []).some((item) => item && item.urgent) ? 'urgent' : 'notice'
        },
        // 弹窗标题：含紧急事项时提示紧急，否则按来源区分
        reminderTitle() {
            if (this.reminderLevel === 'urgent') {
                return '有紧急事项待处理'
            }
            return this.reminderMode === 'urgent' ? '有待办事项待处理' : '有新的待办事项'
        },
        // 弹窗副标题
        reminderSubtitle() {
            if (this.reminderLevel === 'urgent') {
                return `含 ${this.reminderUrgentTotal} 项紧急待办，请尽快处理`
            }
            if (this.reminderMode === 'urgent') {
                return `共 ${this.reminderTotal} 项待办，请及时处理`
            }
            return `待办数量由 ${this.noticeFrom} 增加到 ${this.noticeTo}，请及时处理`
        },
        // 弹窗内待办条目合计数量
        reminderTotal() {
            return (this.reminderList || []).reduce((sum, item) => sum + (Number(item.count) || 0), 0)
        },
        // 弹窗内紧急待办的合计数量
        reminderUrgentTotal() {
            return (this.reminderList || []).reduce(
                (sum, item) => sum + (item && item.urgent ? Number(item.count) || 0 : 0),
                0
            )
        },
    },
    mounted() {
        this.localCount = this.readLocalCount()
        // 首次请求决定是否展示铃铛；接口返回无待办页面权限时立即停止轮询
        this.getTodo()
        this.startPoll()
    },
    beforeUnmount() {
        this.stopPoll()
        this.unbindGesturePlay()
        this.stopUrgentAudio()
        this.audio = null
    },
    methods: {
        hanldHome() {
            window.open('/');
        },
        // 应用接口返回的可见性：无权限时隐藏铃铛并停止轮询与提醒音（内部启停方法均幂等）
        applyEnabled(enabled) {
            this.todoVisible = enabled
            if (enabled) {
                this.startPoll()
                // 提前加载提醒音频，弹窗出现时能立刻发声
                this.ensureAudio()
                // 绑定用户手势，用于补播被浏览器拦截的提示音
                this.bindGesturePlay()
                return
            }
            this.stopPoll()
            this.unbindGesturePlay()
            this.stopUrgentAudio()
            this.reminderVisible = false
        },
        // 开始轮询
        startPoll() {
            if (this.pollTimer) {
                return
            }
            // 每 10 秒查询服务端待办数量，与本地记录比对
            this.pollTimer = setInterval(() => this.getTodo(), TODO_POLL_INTERVAL)
        },
        // 停止轮询
        stopPoll() {
            if (!this.pollTimer) {
                return
            }
            clearInterval(this.pollTimer)
            this.pollTimer = null
        },
        // 获取待办事项
        // notify: 是否比对数量并在服务端变多时弹窗提醒；展开待办面板时只刷新列表并记为已读
        getTodo(notify = true) {
            if (this.loading) {
                return
            }
            this.loading = true
            this.$xbcode.useGet('app/xbCode/admin/Index/todo').then((res) => {
                const data = (res && res.data) || {}
                // 旧版接口未返回 enabled，按可见处理
                this.applyEnabled(data.enabled !== false)
                if (!this.todoVisible) {
                    this.todoList = []
                    this.todoCount = 0
                    this.urgentList = []
                    return
                }
                const list = Array.isArray(data.list) ? data.list : []
                const count = Number(data.count) || 0
                if (notify) {
                    this.checkNotice(list, count)
                } else {
                    this.saveLocalCount(count, list)
                }
                this.todoList = list
                this.todoCount = count
                this.checkUrgent(list)
            }).catch(() => {
                // 请求失败保留本地已加载的待办，避免数量被清零后误触发提醒
                if (this.localCount < 0) {
                    this.todoList = []
                    this.todoCount = 0
                }
            }).finally(() => {
                this.loading = false
            })
        },
        // 与本地记录的数量比对：服务端更多时弹窗提醒，并把服务端数量记为本地记录
        checkNotice(list, count) {
            const base = this.localCount
            // 首次查询或今日已勾选不再提醒时，只记录数量
            if (base < 0 || this.isNoticeDismissedToday()) {
                this.saveLocalCount(count, list)
                return
            }
            if (count <= base) {
                this.saveLocalCount(count, list)
                return
            }
            // 弹窗已展示时：紧急弹窗先不叠加，等其关闭后的轮询再提醒；新增弹窗只刷新数量
            if (this.reminderVisible) {
                if (this.reminderMode === 'notice') {
                    this.noticeTo = count
                    this.saveLocalCount(count, list)
                }
                return
            }
            this.noticeList = this.diffList(list)
            this.noticeFrom = base
            this.noticeTo = count
            this.saveLocalCount(count, list)
            this.openReminder('notice')
        },
        // 读取本地记录的待办数量，无记录时返回 -1
        readLocalCount() {
            try {
                const raw = localStorage.getItem(this.localCountKey)
                if (raw === null || raw === '') {
                    return -1
                }
                const value = Number(raw)
                return Number.isFinite(value) && value >= 0 ? value : -1
            } catch (e) {
                return -1
            }
        },
        // 保存服务端返回的待办数量与条目快照作为本地记录
        saveLocalCount(count, list) {
            this.localCount = count
            this.localList = Array.isArray(list) ? list : []
            try {
                localStorage.setItem(this.localCountKey, String(count))
            } catch (e) { }
        },
        // 对比本地基准快照，取出数量新增的待办
        diffList(list) {
            const added = (list || []).filter((item) => {
                if (!item) {
                    return false
                }
                const old = this.localList.find((row) => row && row.title === item.title && row.path === item.path)
                return !old || Number(item.count) > Number(old.count)
            })
            // 无法对应到具体条目（例如条目被重命名）时，展示全部待办
            return added.length ? added : list || []
        },
        // 今日是否已勾选新增待办不再提醒
        isNoticeDismissedToday() {
            return this.isDismissedToday('notice')
        },
        // 打开提醒弹窗：统一入口，同一时间只会有一个弹窗
        openReminder(mode) {
            this.reminderMode = mode
            this.dontRemindToday = false
            this.reminderVisible = true
            // 两种提醒都播放提示音（新增待办与紧急待办都需要提醒）
            this.playUrgentAudio()
        },
        // 关闭提醒弹窗；勾选了「今日不再提醒」则当天该模式下不再弹窗
        handleReminderClose() {
            if (this.dontRemindToday) {
                try {
                    localStorage.setItem(this.dismissKeyOf(this.reminderMode), this.today())
                } catch (e) { }
            }
            // 紧急提醒按会话标记已提醒过，新增待办由数量基准控制
            if (this.reminderMode === 'urgent') {
                try {
                    sessionStorage.setItem(this.sessionKey, this.today())
                } catch (e) { }
            }
            this.reminderVisible = false
            this.noticeList = []
            this.stopUrgentAudio()
        },
        // 点击弹窗内的待办条目：关闭弹窗并跳转
        handleReminderJump(item) {
            this.handleReminderClose()
            this.handleJump(item)
        },
        // 检查紧急待办并弹窗提醒
        checkUrgent(list) {
            this.urgentList = (list || []).filter((item) => item && item.urgent)
            if (!this.urgentList.length) {
                return
            }
            // 已有弹窗展示中时先不叠加，等其关闭后的轮询再提醒
            if (this.reminderVisible) {
                return
            }
            // 今日已选择不再提醒，或本次会话已提醒过，则跳过
            if (this.isDismissedToday('urgent') || this.isNotifiedInSession()) {
                return
            }
            this.openReminder('urgent')
        },
        // 指定模式「今日不再提醒」的存储键
        dismissKeyOf(mode) {
            return mode === 'urgent'
                ? `xb_todo_urgent_dismiss_${this.adminName}`
                : `xb_todo_notice_dismiss_${this.adminName}`
        },
        // 指定模式今日是否已选择不再提醒
        isDismissedToday(mode) {
            try {
                return localStorage.getItem(this.dismissKeyOf(mode)) === this.today()
            } catch (e) {
                return false
            }
        },
        // 本次会话是否已提醒过
        isNotifiedInSession() {
            try {
                return sessionStorage.getItem(this.sessionKey) === this.today()
            } catch (e) {
                return false
            }
        },
        // 今日日期标记
        today() {
            const d = new Date()
            return `${d.getFullYear()}-${d.getMonth() + 1}-${d.getDate()}`
        },
        // 播放提醒音频
        playUrgentAudio() {
            const audio = this.ensureAudio()
            if (!audio) {
                return
            }
            // 每次提醒都从头发声，避免上次播放未结束导致听不到
            try {
                audio.muted = false
                audio.currentTime = 0
            } catch (e) { }
            const result = audio.play()
            if (result && typeof result.then === 'function') {
                result.then(() => {
                    this.audioPending = false
                }).catch(() => {
                    // 浏览器自动播放策略拦截：保持待播放状态，等用户任意交互后补播
                    this.audioPending = true
                    this.bindGesturePlay()
                })
            }
        },
        // 创建并复用音频对象，文件缺失或解码失败时标记为不可用
        ensureAudio() {
            if (this.audioBroken) {
                return null
            }
            if (!this.audio) {
                try {
                    const audio = new Audio()
                    audio.preload = 'auto'
                    audio.loop = false
                    // 音频文件缺失或解码失败后不再重试，避免反复触发错误
                    audio.addEventListener('error', () => {
                        this.audioBroken = true
                        this.audioPending = false
                    })
                    audio.src = URGENT_AUDIO
                    audio.load()
                    this.audio = audio
                } catch (e) {
                    this.audioBroken = true
                    return null
                }
            }
            return this.audio
        },
        // 绑定用户手势：首次点击 / 按键时补播被拦截的提示音
        bindGesturePlay() {
            if (this.gestureBound) {
                return
            }
            this.gestureBound = true
            const handler = () => {
                this.unbindGesturePlay()
                if (this.audioPending) {
                    this.audioPending = false
                    this.playUrgentAudio()
                }
            }
            this.gestureHandler = handler
            document.addEventListener('click', handler, true)
            document.addEventListener('keydown', handler, true)
            document.addEventListener('touchstart', handler, true)
        },
        // 移除手势监听
        unbindGesturePlay() {
            if (this.gestureHandler) {
                document.removeEventListener('click', this.gestureHandler, true)
                document.removeEventListener('keydown', this.gestureHandler, true)
                document.removeEventListener('touchstart', this.gestureHandler, true)
                this.gestureHandler = null
            }
            this.gestureBound = false
        },
        // 停止提醒音频
        stopUrgentAudio() {
            if (this.audio) {
                try {
                    this.audio.pause()
                    this.audio.currentTime = 0
                } catch (e) { }
            }
        },
        // 跳转待办处理页面
        handleJump(item) {
            const path = (item && item.path) || ''
            if (!path) {
                return
            }
            // 外部链接直接新窗口打开
            if (path.includes('http://') || path.includes('https://')) {
                window.open(path)
                return
            }
            this.$router.push({
                path,
                query: {
                    ...(item.query || {}),
                    redirect: this.$route.fullPath,
                },
            })
        },
    },
}
</script>

<style lang="scss">
/* 弹层内容会被 teleport 到 body，此处统一使用唯一类名定义全局样式 */
.xb-todo-toolbar {
    height: 100%;
    overflow: hidden;

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
                    -webkit-animation: xb-todo-logo-animation .3s ease-in-out;
                    animation: xb-todo-logo-animation .3s ease-in-out;
                }
            }
        }

        .todo-icon-wrap {
            position: relative;
            display: flex;
            align-items: center;

            .todo-badge {
                position: absolute;
                top: -8px;
                right: -12px;
                min-width: 16px;
                height: 16px;
                padding: 0 4px;
                box-sizing: border-box;
                border-radius: 8px;
                background: var(--el-color-danger, #f53f3f);
                color: #fff;
                font-size: 12px;
                line-height: 16px;
                text-align: center;
                white-space: nowrap;
                box-shadow: 0 0 0 1px var(--el-bg-color, #fff);
            }
        }
    }
}

.xb-todo-popper {
    padding: 0 !important;

    .todo-panel {
        overflow: hidden;
        border-radius: 4px;

        .todo-panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            border-bottom: 1px solid var(--el-border-color-lighter, #e5e6eb);
            background: var(--el-fill-color-light, #f7f8fa);

            .todo-panel-title {
                font-size: 14px;
                font-weight: 600;
                color: var(--el-text-color-primary, #1d2129);
            }

            .todo-panel-total {
                font-size: 12px;
                color: var(--el-text-color-secondary, #86909c);
            }
        }

        .todo-panel-body {
            max-height: 320px;
            overflow-y: auto;
            padding: 6px 0;
        }

        .todo-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 14px;
            cursor: pointer;
            transition: background 0.2s ease;

            &:hover {
                background: var(--el-fill-color-light, #f2f3f5);
            }

            .todo-item-icon {
                flex: none;
                width: 28px;
                height: 28px;
                display: flex;
                justify-content: center;
                align-items: center;
                border-radius: 6px;
                background: #eaf1fe;

                /* 紧急事项用红色区分 */
                &.is-urgent {
                    background: #feeceb;
                }
            }

            .todo-item-info {
                flex: 1;
                min-width: 0;

                .todo-item-title {
                    font-size: 13px;
                    line-height: 18px;
                    color: var(--el-text-color-primary, #1d2129);
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                }

                .todo-item-desc {
                    margin-top: 2px;
                    font-size: 12px;
                    line-height: 16px;
                    color: var(--el-text-color-secondary, #86909c);
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                }
            }

            .todo-item-count {
                flex: none;
                min-width: 24px;
                height: 20px;
                padding: 0 6px;
                box-sizing: border-box;
                border-radius: 10px;
                background: #eaf1fe;
                color: #2468f2;
                font-size: 12px;
                line-height: 20px;
                text-align: center;

                &.is-urgent {
                    background: #feeceb;
                    color: #f53f3f;
                }
            }
        }

        .todo-empty {
            padding: 24px 0;
            text-align: center;
            font-size: 13px;
            color: var(--el-text-color-secondary, #86909c);
        }
    }
}

/* 待办提醒弹窗：紧急待办与新增待办共用，垂直水平居中展示 */
.xb-reminder-dialog {
    --xb-accent: #2468f2;
    --xb-accent-deep: #1d4ed8;
    --xb-accent-soft: #eaf1fe;
    --xb-accent-line: #d5e3fd;
    --xb-accent-shadow: rgba(36, 104, 242, .32);
    --xb-head-bg: linear-gradient(135deg, #eef4ff 0%, #f6faff 100%);
    --xb-ring-shadow: 0 10px 22px rgba(36, 104, 242, .28);
    max-width: 94vw;
    max-height: 88vh;
    padding: 0 !important;
    border-radius: 20px;
    overflow: hidden;
    border: none;
    background: #fff;
    box-shadow: 0 28px 80px rgba(17, 24, 39, .2), 0 2px 12px rgba(17, 24, 39, .06);

    &.is-urgent {
        --xb-accent: #f53f3f;
        --xb-accent-deep: #cb2634;
        --xb-accent-soft: #feeceb;
        --xb-accent-line: #fbd6d3;
        --xb-accent-shadow: rgba(245, 63, 63, .3);
        --xb-head-bg: linear-gradient(135deg, #fff3f0 0%, #fff9f2 100%);
        --xb-ring-shadow: 0 10px 22px rgba(245, 63, 63, .26);
    }

    .el-dialog__header {
        display: none;
    }

    .el-dialog__body {
        padding: 0;
    }

    .el-dialog__footer {
        display: none;
    }

    .reminder-panel {
        display: flex;
        flex-direction: column;
        max-height: 88vh;
        background: #fff;
    }

    .reminder-head {
        position: relative;
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 28px 28px 24px;
        background: var(--xb-head-bg);
        overflow: hidden;

        /* 右上角柔光装饰，提升质感 */
        &::after {
            content: '';
            position: absolute;
            top: -76px;
            right: -52px;
            width: 210px;
            height: 210px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, .95) 0%, rgba(255, 255, 255, 0) 70%);
            pointer-events: none;
        }
    }

    .reminder-ring {
        position: relative;
        flex: none;
        width: 52px;
        height: 52px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--xb-accent) 0%, var(--xb-accent-deep) 100%);
        box-shadow: var(--xb-ring-shadow);

        &::before {
            content: '';
            position: absolute;
            inset: -8px;
            border-radius: 50%;
            border: 2px solid var(--xb-accent);
            opacity: .28;
            animation: xb-reminder-pulse 1.8s ease-out infinite;
        }
    }

    .reminder-head-text {
        position: relative;
        z-index: 1;
        flex: 1;
        min-width: 0;
    }

    .reminder-amount {
        position: relative;
        z-index: 1;
        flex: none;
        display: flex;
        align-items: baseline;
        gap: 3px;
        padding: 6px 14px;
        border-radius: 13px;
        background: rgba(255, 255, 255, .78);
        border: 1px solid var(--xb-accent-line);
        color: var(--xb-accent);
    }

    .reminder-amount-num {
        font-size: 19px;
        font-weight: 700;
        line-height: 22px;
        font-variant-numeric: tabular-nums;
    }

    .reminder-amount-unit {
        font-size: 11px;
        line-height: 16px;
        opacity: .85;
    }

    .reminder-title {
        font-size: 20px;
        font-weight: 600;
        line-height: 28px;
        letter-spacing: .2px;
        color: #1d2129;
    }

    .reminder-subtitle {
        margin-top: 4px;
        font-size: 13px;
        line-height: 19px;
        color: #86909c;
    }

    .reminder-body {
        flex: 1;
        min-height: 0;
        padding: 10px 14px;
    }

    .reminder-list {
        max-height: 340px;
        overflow-y: auto;
        padding: 4px 0;

        &::-webkit-scrollbar {
            width: 6px;
        }

        &::-webkit-scrollbar-thumb {
            border-radius: 3px;
            background: #dfe2e8;
        }
    }

    .reminder-item {
        position: relative;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 13px 14px;
        border-radius: 12px;
        cursor: pointer;
        transition: background .18s ease, transform .18s ease;

        &:hover {
            background: #f7f9fc;

            .reminder-item-arrow {
                transform: translateX(2px);
                opacity: 1;
            }
        }

        &:active {
            transform: scale(.985);
        }
    }

    .reminder-item-icon {
        flex: none;
        width: 40px;
        height: 40px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 12px;
        /* 同一条列表里可能同时有紧急（红）与普通（蓝）事项，按条配色 */
        background: #eaf1fe;
        border: 1px solid #d5e3fd;

        &.is-urgent {
            background: #feeceb;
            border-color: #fbd6d3;
        }
    }

    .reminder-item-info {
        flex: 1;
        min-width: 0;
    }

    .reminder-item-title {
        font-size: 15px;
        font-weight: 500;
        line-height: 22px;
        color: #1d2129;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .reminder-item-desc {
        margin-top: 3px;
        font-size: 12px;
        line-height: 17px;
        color: #86909c;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .reminder-item-count {
        flex: none;
        min-width: 30px;
        height: 24px;
        padding: 0 10px;
        box-sizing: border-box;
        border-radius: 12px;
        background: #eaf1fe;
        color: #2468f2;
        font-size: 13px;
        font-weight: 600;
        line-height: 24px;
        text-align: center;

        &.is-urgent {
            background: #feeceb;
            color: #f53f3f;
        }
    }

    .reminder-item-arrow {
        flex: none;
        opacity: .45;
        transition: transform .18s ease, opacity .18s ease;
    }

    .reminder-empty {
        padding: 56px 0;
        text-align: center;
        font-size: 13px;
        color: #86909c;
    }

    .reminder-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 16px 24px 20px;
        border-top: 1px solid #f2f3f5;
    }

    .reminder-check {
        margin-right: 0;

        .el-checkbox__label {
            padding-left: 6px;
            font-size: 13px;
            color: #86909c;
        }

        &.is-checked .el-checkbox__label {
            color: #4e5969;
        }
    }

    .reminder-btn {
        width: 116px;
        height: 38px;
        border: none;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--xb-accent) 0%, var(--xb-accent-deep) 100%);
        box-shadow: 0 6px 16px var(--xb-accent-shadow);
        font-size: 14px;
        font-weight: 500;

        &:hover,
        &:focus {
            background: linear-gradient(135deg, var(--xb-accent-deep) 0%, var(--xb-accent) 100%);
            opacity: .96;
        }
    }
}

@keyframes xb-reminder-pulse {
    0% {
        opacity: .55;
        transform: scale(.82);
    }

    70% {
        opacity: 0;
        transform: scale(1.22);
    }

    100% {
        opacity: 0;
        transform: scale(1.22);
    }
}

@keyframes xb-todo-logo-animation {
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
