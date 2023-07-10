FROM osixia/openldap

ENV LDAP_ORGANISATION="LDAP ORGANISATION"
ENV LDAP_DOMAIN="ldapinjection.local"
ENV LDAP_ADMIN_PASSWORD="admin"

COPY user1.ldif /container/service/slapd/assets/user1.ldif
COPY users.ldif /container/service/slapd/assets/users.ldif

VOLUME /var/lib/ldap

#CMD ["/usr/local/bin/startup.sh"]