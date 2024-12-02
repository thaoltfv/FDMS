FROM ghcr.io/fastvalue/fastpro_php7:latest

ARG USER_NAME=www
ARG USER_ID=1000
ARG GROUP_NAME=www
ARG GROUP_ID=1000
ARG DEFAULT_TZ=Asia/Ho_Chi_Minh

ENV USER_NAME=${USER_NAME}
ENV USER_ID=${USER_ID}
ENV GROUP_NAME=${GROUP_NAME}
ENV GROUP_ID=${GROUP_ID}
ENV DEFAULT_TZ=${DEFAULT_TZ}

## Default timezone
ENV TZ=${DEFAULT_TZ}
RUN apk add -U tzdata
RUN cp /usr/share/zoneinfo/${DEFAULT_TZ} /etc/localtime

## User and Group
RUN addgroup -g ${GROUP_ID} ${GROUP_NAME} \
 && adduser -u ${USER_ID} -G ${GROUP_NAME} -h /srv/www -s /bin/sh -D ${USER_NAME}

RUN mkdir -p /etc/supervisor.d/conf.d
COPY .ops/etc/ /etc
COPY .ops/start.sh /start.sh
RUN chmod +x /start.sh

## replace fpm user_name
RUN sed "s/nginx/${USER_NAME}/g" /etc/php7/php-fpm.d/www.conf-template > /etc/php7/php-fpm.d/www.conf \
 && sed "s/nginx/${USER_NAME}/g" /etc/supervisor/conf.d/scheduler.conf-template > /etc/supervisor/conf.d/scheduler.conf \
 && rm /etc/php7/php-fpm.d/www.conf-template \
 && rm /etc/supervisor/conf.d/scheduler.conf-template

RUN chown ${USER_NAME}:${GROUP_NAME} /srv/www
WORKDIR /srv/www
VOLUME /srv/www/storage

USER ${USER_NAME}:${GROUP_NAME}
RUN mkdir -p /srv/www/database/seeds

COPY --chown=${USER_NAME}:${GROUP_NAME} ./composer.* /srv/www/
RUN composer install --no-scripts

COPY --chown=${USER_NAME}:${GROUP_NAME} . /srv/www/
RUN composer install

## link public storage
RUN ln -s /srv/www/storage/app/public /srv/www/public/storage

USER root:root
EXPOSE 80

CMD ["/start.sh"]
ENTRYPOINT ["/bin/bash", "-c"]
