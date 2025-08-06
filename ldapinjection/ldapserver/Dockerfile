FROM osixia/openldap

ENV LDAP_ORGANISATION="LDAP ORGANISATION"
ENV LDAP_DOMAIN="ldapinjection.local"
ENV LDAP_ADMIN_PASSWORD="admin"

COPY bootstrap.ldif /container/service/slapd/assets/config/bootstrap/ldif/custom/

VOLUME /var/lib/ldap

#CMD ["/usr/local/bin/startup.sh"]