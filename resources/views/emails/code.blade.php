<div class='template'>
    <div class='code'>
        <div class='mb-8 f-28 f-b'>verification</div>
        <div class='mb-8 f-16'>Your verification code</div>
        <div class='mb-8 f-28 f-b col-ffcc33'>{{$code}}</div>
        <div class='mb-8 f-16 lh-1'>The verification code will be valid for {{ $time }} second. Please do not share this code with others.</div>
        <div class='mb-8 f-16 lh-1'>If you have not sent this email, please ignore it.</div>
        <div class='mb-8 f-16 lh-1'>This is an automated message, please do not reply</div>
        <div class='mb-8 f-16 lh-1'>Please be aware of phishing sites and always make sure you arevisiting the official <a href='{{$url}}' class='col-ffcc33'>{{$url}}</a> website when entering sensitive data.</div>
    </div>
</div>
<style>
    * {
        margin: 0;
        padding: 0;
    }

    .template {
        width: 100%;
    }

    .code {
        padding: 20px 5%;
        text-align: left;
    }

    .col-ffcc33 {
        color: #ffcc33;
    }

    .mb-8 {
        margin-bottom: 8px;
    }

    .mb-20 {
        margin-bottom: 20px;
    }

    .f-b {
        font-weight: bold;
    }

    .f-16 {
        font-size: 16px;
    }

    .f-28 {
        font-size: 28px;
    }

    .lh-1 {
        line-height: 1.5;
    }
</style>
