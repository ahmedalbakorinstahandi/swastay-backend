cd /
cd home/sawastay-backend/htdocs/backend.sawastay.com
git pull

nano deploy.sh
chmod +x deploy.sh 

ssh root@69.62.114.139 './deploy.sh'