@php
    $userId = $user->id ?? null;
    $name = $user->name ?? '—';
$uuid = $user->uuid ?? null;
$email = $user->email ?? null;
$address = $user->address ?? null;
$loginType = $user->login_type ?? null;
$loginLabels = [
'email' => '邮箱',
'uuid' => '游客',
'address' => '钱包',
];
$loginLabel = $loginLabels[$loginType] ?? ($loginType ?: '—');
@endphp

<div style="
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    padding: 10px 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    min-width: 220px;
">
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 8px;">
        <div style="color: #fff; font-size: 14px; font-weight: 600; word-break: break-all;">
            <i class="feather icon-user" style="margin-right: 4px; opacity: 0.85;"></i>{{ $name }}
        </div>
        <span style="
            flex-shrink: 0;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 10px;
            background: rgba(255,255,255,0.22);
            color: rgba(255,255,255,0.95);
            border: 1px solid rgba(255,255,255,0.25);
        ">{{ $loginLabel }}</span>
    </div>

    <div style="
        background: rgba(255,255,255,0.12);
        border-radius: 6px;
        padding: 6px 8px;
        border: 1px solid rgba(255,255,255,0.15);
        font-size: 11px;
        line-height: 1.6;
    ">
        <div style="color: rgba(255,255,255,0.95); word-break: break-all;">
            <i class="feather icon-user" style="width: 14px; margin-right: 4px; opacity: 0.75;"></i>
            <span style="opacity: 0.75;">ID</span>
            {{ $userId ?? '—' }}
        </div>
        <div style="color: rgba(255,255,255,0.95); word-break: break-all; margin-top: 4px;" title="{{ $uuid }}">
            <i class="feather icon-hash" style="width: 14px; margin-right: 4px; opacity: 0.75;"></i>
            <span style="opacity: 0.75;">UUID</span>
            {{ $uuid ? \Illuminate\Support\Str::limit($uuid, 36) : '—' }}
        </div>
        <div style="color: rgba(255,255,255,0.95); word-break: break-all; margin-top: 4px;" title="{{ $email }}">
            <i class="feather icon-mail" style="width: 14px; margin-right: 4px; opacity: 0.75;"></i>
            <span style="opacity: 0.75;">邮箱</span>
            {{ $email ? \Illuminate\Support\Str::limit($email, 40) : '—' }}
        </div>
        <div style="color: rgba(255,255,255,0.95); word-break: break-all; margin-top: 4px;" title="{{ $address }}">
            <i class="feather icon-link" style="width: 14px; margin-right: 4px; opacity: 0.75;"></i>
            <span style="opacity: 0.75;">地址</span>
            {{ $address ? \Illuminate\Support\Str::limit($address, 42) : '—' }}
        </div>
    </div>
</div>
