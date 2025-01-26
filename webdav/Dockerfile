FROM bytemark/webdav

ENV AUTH_TYPE=Digest \
    USERNAME=admin \
    PASSWORD=tinkerbell

EXPOSE 80

COPY ./src /var/lib/dav/data

VOLUME ["/var/lib/dav/data"]