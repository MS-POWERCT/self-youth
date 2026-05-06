{{-- resources/views/admin/components/user-info-card.blade.php --}}
@props(['user'])

@php
// 判断用户是否存在
if (!$user || !isset($user->id)) {
$name = '用户不存在';
$address = '用户不存在';
$userId = '未知';
} else {
$name = $user->name ?? '未设置姓名';
$address = $user->address ?? '未设置地址';
$created_at = $user->created_at;
$userId = $user->id ?? '未知';
$referrer_address = $user->referrer_address ?? '无';
}
@endphp

<div class="user-info-card" style="
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    padding: 6px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    position: relative;
    overflow: hidden;
    margin-bottom: 8px;
">

    <!-- 信息网格布局 -->
    <div style="
        background: rgba(255,255,255,0.1);
        border-radius: 6px;
        padding: 4px;
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255,255,255,0.15);
    ">
        <!-- 2列网格 -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px 12px; font-size: 11px;">
            <!-- 用户id-->
            <div style="grid-column: 1 / -1; color: rgba(255,255,255,0.95); word-break: break-all;" title="{{ $address }}">
                <i class="fa fa-map-marker" style="margin-right: 4px; opacity: 0.7; width: 12px;"></i>
                <span style="opacity: 0.8;">用户id:</span> {{ $userId }}
            </div>
            <!-- 地址 - 跨2列 -->
            <div style="grid-column: 1 / -1; color: rgba(255,255,255,0.95); word-break: break-all;" title="{{ $address }}">
                <i class="fa fa-map-marker" style="margin-right: 4px; opacity: 0.7; width: 12px;"></i>
                <span style="opacity: 0.8;">地址:</span> {{ Str::limit($address, 60) }}
            </div>


            <!-- 注册时间 -->
            <div style="grid-column: 1 / -1; color: rgba(255,255,255,0.95); word-break: break-all;">
                <i class="fa fa-map-marker" style="margin-right: 4px; opacity: 0.7; width: 12px;"></i>
                <span style="opacity: 0.8;">注册时间:</span>{{ \Carbon\Carbon::parse($created_at)->format('Y-m-d H:i') }}
            </div>

            <!-- 推荐人 -->
            <div style="grid-column: 1 / -1; color: rgba(255,255,255,0.95); word-break: break-all;" title="{{ $referrer_address }}">
                <i class="fa fa-map-marker" style="margin-right: 4px; opacity: 0.7; width: 12px;"></i>
                <span style="opacity: 0.8;">推荐地址:</span> {{ Str::limit($referrer_address, 60) }}
            </div>
        </div>
    </div>
</div>