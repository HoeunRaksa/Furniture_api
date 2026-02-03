$ServerIP = "54.165.46.134"
$KeyPath = "C:\Users\hoeun\.ssh\K3.pem"

Write-Host "Connecting to $ServerIP using $KeyPath..." -ForegroundColor Green
ssh -i $KeyPath ubuntu@$ServerIP
 