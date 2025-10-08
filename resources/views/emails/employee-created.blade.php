<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Employee Added</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .employee-details {
            background-color: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            border: 1px solid #e5e7eb;
        }
        .detail-row {
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #6b7280;
            display: inline-block;
            width: 120px;
        }
        .value {
            color: #111827;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4F46E5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Employee Added</h1>
    </div>
    
    <div class="content">
        <p>Hello {{ $company->name }},</p>
        
        <p>A new employee has been successfully added to your company in the Mini-CRM system.</p>
        
        <div class="employee-details">
            <h2 style="margin-top: 0; color: #4F46E5;">Employee Details</h2>
            
            <div class="detail-row">
                <span class="label">Name:</span>
                <span class="value">{{ $employee->full_name }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Email:</span>
                <span class="value">{{ $employee->email }}</span>
            </div>
            
            @if($employee->phone)
            <div class="detail-row">
                <span class="label">Phone:</span>
                <span class="value">{{ $employee->phone }}</span>
            </div>
            @endif
            
            <div class="detail-row">
                <span class="label">Company:</span>
                <span class="value">{{ $company->name }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Added on:</span>
                <span class="value">{{ $employee->created_at->format('F j, Y \a\t g:i A') }}</span>
            </div>
        </div>
        
        <p>You can view and manage this employee's information in your Mini-CRM dashboard.</p>
        
        <center>
            <a href="{{ config('app.url') }}/employees/{{ $employee->id }}" class="button">
                View Employee Details
            </a>
        </center>
    </div>
    
    <div class="footer">
        <p>This is an automated message from Mini-CRM.</p>
        <p>&copy; {{ date('Y') }} Mini-CRM. All rights reserved.</p>
    </div>
</body>
</html>

