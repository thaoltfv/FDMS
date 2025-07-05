FROM postgres:17-alpine3.22

COPY ./docker-entrypoint-initdb.d /docker-entrypoint-initdb.d

COPY ./entrypoint.sh /entrypoint.sh

EXPOSE 5432

ENTRYPOINT ["/entrypoint.sh"]
CMD ["postgres"]
