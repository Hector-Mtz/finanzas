worker_processes  1;

events {
    worker_connections  1024;
}
http {
    include       mime.types;
    sendfile        on;
    keepalive_timeout  65;
    server {
        listen LISTEN_PORT default_server;
        server_name _;
        root /app/public;
        index index.php;
        charset utf-8;
        location / {
            try_files $uri $uri/ /index.php?$query_string;
            gzip_static on;
        }

        location = /favicon.ico { access_log off; log_not_found off; }
        location = /robots.txt  { access_log off; log_not_found off; }
        access_log /dev/stdout;
        error_log /dev/stderr;
        sendfile off;
        client_max_body_size 100m;
        location ~ \.php$ {
            fastcgi_split_path_info ^(.+\.php)(/.+)$;
            fastcgi_pass 127.0.0.1:9000;
            fastcgi_index index.php;
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            fastcgi_intercept_errors off;
            fastcgi_buffering off;
        }

        error_page   500 502 503 504  /50x.html;
        location = /50x.html {
            root   html;
        }

    }
    #include /etc/nginx/sites-enabled/*;
}

daemon off;
