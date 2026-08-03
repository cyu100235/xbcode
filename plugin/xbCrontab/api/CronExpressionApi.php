<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCrontab\api;

use plugin\xbCrontab\enum\CronPresetsEnum;

/**
 * Cron表达式接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class CronExpressionApi
{
    /**
     * 实例化
     * @return CronExpressionApi
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make()
    {
        $class = new static;
        return $class;
    }

    /**
     * 生成自定义Cron表达式
     * @param string $cycleType 周期类型 (minute/hour/day/week/month/year)
     * @param int $interval 间隔值
     * @param string $time 时间 (HH:MM格式)
     * @param int $weekday 星期几 (0-6，仅week类型使用)
     * @return array ['expression' => string, 'desc' => string]
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function buildCronExpression(string $cycleType, int $interval, string $time = '', int $weekday = 0, int $yearDay = 1): array
    {
        $timeParts = $time ? explode(':', $time) : [0, 0];
        $hour = intval($timeParts[0] ?? 0);
        $minute = intval($timeParts[1] ?? 0);
        
        $expression = '';
        $desc = '';
        
        switch ($cycleType) {
            case 'minute':
                $interval = max(1, $interval);
                $expression = "*/{$interval} * * * * *";
                $desc = "每{$interval}分钟";
                break;
                
            case 'hour':
                $interval = max(1, $interval);
                $expression = "0 */{$interval} * * * *";
                $desc = "每{$interval}小时";
                break;
                
            case 'day':
                // 间隔值为0或1时表示每天一次
                if ($interval <= 1) {
                    $expression = "{$minute} {$hour} * * *";
                    $desc = "每天 {$hour}:" . str_pad($minute, 2, '0', STR_PAD_LEFT);
                } else {
                    $expression = "{$minute} {$hour} */{$interval} * *";
                    $desc = "每{$interval}天 {$hour}:" . str_pad($minute, 2, '0', STR_PAD_LEFT);
                }
                break;
                
            case 'week':
                $weekday = max(0, min(6, $weekday));
                $weekNames = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];
                $expression = "{$minute} {$hour} * * {$weekday}";
                $desc = "每周{$weekNames[$weekday]}";
                if ($time) {
                    $desc .= " {$hour}:" . str_pad($minute, 2, '0', STR_PAD_LEFT);
                }
                break;
                
            case 'month':
                $day = max(1, min(31, $interval));
                $expression = "{$minute} {$hour} {$day} * *";
                $desc = "每月{$day}日";
                if ($time) {
                    $desc .= " {$hour}:" . str_pad($minute, 2, '0', STR_PAD_LEFT);
                }
                break;
                
            case 'year':
                $month = max(1, min(12, $interval));
                $day = max(1, min(31, $yearDay));
                $monthNames = ['', '一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'];
                $expression = "{$minute} {$hour} {$day} {$month} *";
                $desc = "每年{$monthNames[$month]} {$day}日";
                if ($time) {
                    $desc .= " {$hour}:" . str_pad($minute, 2, '0', STR_PAD_LEFT);
                }
                break;
                
            default:
                $expression = "*/5 * * * * *";
                $desc = '每5分钟';
        }
        
        return ['expression' => $expression, 'desc' => $desc];
    }

    /**
     * 从Cron表达式解析周期类型和参数
     * @param string $expression Cron表达式
     * @return array ['cycle_type' => string, 'interval' => int, 'time' => string, 'weekday' => int]
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function parseCronToParams(string $expression): array
    {
        $parts = preg_split('/\s+/', trim($expression));
        $count = count($parts);
        
        $result = [
            'cycle_type' => 'minute',
            'interval' => 1,
            'time' => '00:00',
            'weekday' => 0,
        ];
        
        // 5位格式: 分 时 日 月 周
        // 6位格式: 秒 分 时 日 月 周
        if ($count >= 5) {
            // 默认按5位格式解析
            $second = $count === 6 ? $parts[0] : null;
            $minute = $count === 6 ? $parts[1] : $parts[0];
            $hour = $count === 6 ? $parts[2] : $parts[1];
            $day = $count >= 3 ? ($count === 6 ? $parts[3] : $parts[2]) : '*';
            $month = $count >= 4 ? ($count === 6 ? $parts[4] : $parts[3]) : '*';
            $weekday = $count >= 5 ? ($count === 6 ? $parts[5] : $parts[4]) : '*';
            
            // 设置执行时间
            // 只有当 hour 和 minute 都是纯数字时才设置时间
            if ($hour !== '*' && $minute !== '*' && ctype_digit((string)$hour) && ctype_digit((string)$minute)) {
                $result['time'] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minute, 2, '0', STR_PAD_LEFT);
            }
            
            // 判断周期类型
            // 6位格式 - 每分钟: 秒=*/N, 其他=*
            if ($count === 6 && strpos($second, '*/') === 0 && $minute === '*' && $hour === '*' && $day === '*' && $month === '*' && $weekday === '*') {
                $result['cycle_type'] = 'minute';
                $result['interval'] = intval(substr($second, 2));
            }
            // 6位格式 - 每小时: 秒=0, 分=*/N, 其他=*
            elseif ($count === 6 && $second === '0' && strpos($minute, '*/') === 0 && $hour === '*' && $day === '*' && $month === '*' && $weekday === '*') {
                $result['cycle_type'] = 'hour';
                $result['interval'] = intval(substr($minute, 2));
            }
            // 6位格式 - 每天: 秒=0, 分=X, 时=X, 其他=*
            elseif ($count === 6 && $second === '0' && $minute !== '*' && $hour !== '*' && $day === '*' && $month === '*' && $weekday === '*') {
                $result['cycle_type'] = 'day';
                $result['interval'] = 1;
            }
            // 6位格式 - 每月: 秒=0, 分=X, 时=X, 日=X, 月=*, 周=*
            elseif ($count === 6 && $second === '0' && $minute !== '*' && $hour !== '*' && $day !== '*' && $month === '*' && $weekday === '*') {
                $result['cycle_type'] = 'month';
                $result['interval'] = intval($day);
            }
            // 6位格式 - 每周: 秒=0, 分=X, 时=X, 日=*, 月=*, 周=X
            elseif ($count === 6 && $second === '0' && $minute !== '*' && $hour !== '*' && $day === '*' && $month === '*' && $weekday !== '*') {
                $result['cycle_type'] = 'week';
                $result['weekday'] = intval($weekday);
            }
            // 6位格式 - 每年: 秒=0, 分=X, 时=X, 日=X, 月=X, 周=*
            elseif ($count === 6 && $second === '0' && $minute !== '*' && $hour !== '*' && $day !== '*' && $month !== '*' && $weekday === '*') {
                $result['cycle_type'] = 'year';
                $result['interval'] = intval($month);
                $result['day'] = intval($day);
            }
            // 5位格式 - 每分钟: 分=*/N, 其他=*
            elseif ($count === 5 && strpos($minute, '*/') === 0 && $hour === '*' && $day === '*' && $month === '*' && $weekday === '*') {
                $result['cycle_type'] = 'minute';
                $result['interval'] = intval(substr($minute, 2));
            }
            // 5位格式 - 每小时: 分=0, 时=*/N, 其他=*
            elseif ($count === 5 && $minute === '0' && preg_match('/^\*\/[1-9]\d*$/', $hour) && $day === '*' && $month === '*' && $weekday === '*') {
                $result['cycle_type'] = 'hour';
                $result['interval'] = intval(substr($hour, 2));
            }
            // 5位格式 - 每天: 分=X, 时=X, 其他=*
            elseif ($count === 5 && $minute !== '*' && $hour !== '*' && $day === '*' && $month === '*' && $weekday === '*') {
                $result['cycle_type'] = 'day';
                $result['interval'] = 1;
            }
            // 每X天: X X */N * *
            elseif (strpos($day, '*/') === 0 && $month === '*' && $weekday === '*') {
                $result['cycle_type'] = 'day';
                $result['interval'] = intval(substr($day, 2));
            }
            // 每周: X X * * N
            elseif ($day === '*' && $month === '*' && $weekday !== '*') {
                $result['cycle_type'] = 'week';
                $result['weekday'] = intval($weekday);
            }
            // 每月: X X N * *
            elseif ($day !== '*' && $day !== '*' && $month === '*' && $weekday === '*') {
                $result['cycle_type'] = 'month';
                $result['interval'] = intval($day);
            }
            // 每年: X X D N * (5位: 分 时 日 月 周)
            elseif ($count === 5 && $minute !== '*' && $hour !== '*' && $day !== '*' && $month !== '*' && $weekday === '*') {
                $result['cycle_type'] = 'year';
                $result['interval'] = intval($month);
                $result['day'] = intval($day);
            }
        }
        
        return $result;
    }

    /**
     * 从Cron表达式解析生成描述
     * @param string $expression Cron表达式
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function parseCronExpression(string $expression): string
    {
        // 检查是否是预设选项
        $valueMap = CronPresetsEnum::valueMap();
        if (isset($valueMap[$expression])) {
            return $valueMap[$expression];
        }
        
        // 解析自定义表达式
        $parts = preg_split('/\s+/', trim($expression));
        if (count($parts) < 5) {
            return $expression;
        }
        
        // 标准5位格式: 分 时 日 月 周
        // 扩展6位格式: 秒 分 时 日 月 周
        $isExtended = count($parts) === 6;
        
        if ($isExtended) {
            $second = $parts[0];
            $minute = $parts[1];
            $hour = $parts[2];
            $day = $parts[3];
            $month = $parts[4];
            $weekday = $parts[5];
        } else {
            $minute = $parts[0];
            $hour = $parts[1];
            $day = $parts[2];
            $month = $parts[3];
            $weekday = $parts[4];
            $second = '0';
        }
        
        $desc = [];
        
        // 解析时间
        if ($minute === '*' && $hour === '*') {
            $desc[] = '每分钟';
        } elseif (strpos($minute, '*/') === 0) {
            $interval = substr($minute, 2);
            $desc[] = "每{$interval}分钟";
        } elseif (strpos($hour, '*/') === 0) {
            $interval = substr($hour, 2);
            $desc[] = "每{$interval}小时";
            if ($minute !== '0') {
                $desc[] = "第{$minute}分钟";
            }
        } else {
            $hourStr = $hour === '*' ? '?' : $hour;
            $minuteStr = $minute === '*' ? '?' : str_pad($minute, 2, '0', STR_PAD_LEFT);
            if ($hour !== '*' && $minute !== '*') {
                $desc[] = "每天 {$hourStr}:{$minuteStr}";
            } elseif ($hour !== '*') {
                $desc[] = "每小时第{$minuteStr}分钟";
            }
        }
        
        // 解析日期
        if ($day !== '*' && strpos($day, '*/') === 0) {
            $interval = substr($day, 2);
            $desc[] = "每{$interval}天";
        } elseif ($day !== '*') {
            $desc[] = "每月{$day}日";
        }
        
        // 解析月份
        if ($month !== '*' && strpos($month, '*/') === 0) {
            $interval = substr($month, 2);
            $desc[] = "每{$interval}个月";
        } elseif ($month !== '*') {
            $monthNames = [1 => '一月', 2 => '二月', 3 => '三月', 4 => '四月', 5 => '五月', 6 => '六月', 
                          7 => '七月', 8 => '八月', 9 => '九月', 10 => '十月', 11 => '十一月', 12 => '十二月'];
            $desc[] = isset($monthNames[$month]) ? $monthNames[$month] : "{$month}月";
        }
        
        // 解析星期
        if ($weekday !== '*') {
            $weekdayNames = ['0' => '周日', '1' => '周一', '2' => '周二', '3' => '周三', '4' => '周四', '5' => '周五', '6' => '周六'];
            if (isset($weekdayNames[$weekday])) {
                $desc[] = $weekdayNames[$weekday];
            } else {
                $desc[] = "每周{$weekday}";
            }
        }
        
        return empty($desc) ? $expression : implode(' ', $desc);
    }
}
