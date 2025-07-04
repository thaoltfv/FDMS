FROM node:22-alpine3.22

# upgrade npm to latest version
RUN npm install -g npm

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

WORKDIR /app
VOLUME /app

ENTRYPOINT ["/entrypoint.sh"]

CMD [ "sh" ]