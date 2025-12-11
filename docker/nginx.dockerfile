FROM nginx:alpine

# Copy Nginx config
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy entire Laravel project
COPY . /var/www/html/

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
