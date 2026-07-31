FROM node:20-alpine AS assets

WORKDIR /app

COPY package*.json ./
RUN npm ci
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
COPY postcss.config.js ./
COPY tailwind.config.js ./
RUN npm run build

FROM nginx:1.27-alpine

WORKDIR /var/www

COPY public /var/www/public
COPY --from=assets /app/public/build /var/www/public/build
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

RUN rm -rf /var/www/public/storage \
    && mkdir -p /var/www/storage/app/public \
    && ln -s /var/www/storage/app/public /var/www/public/storage
