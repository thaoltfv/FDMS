FROM dxflrs/garage:v2.0.0 AS garage_binary

FROM alpine:3.22

COPY --from=garage_binary /garage /usr/bin/garage

COPY entrypoint.sh /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

CMD ["/usr/bin/garage", "-c", "/etc/garage.toml", "server"]
