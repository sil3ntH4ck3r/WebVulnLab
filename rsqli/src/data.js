const users = [
  { id: 'user-1', email: 'admin@rsqli.local', password: 'admin123', clearance: 3, name: 'Admin', role: 'admin' },
  { id: 'user-2', email: 'senior@rsqli.local', password: 'senior123', clearance: 2, name: 'Senior', role: 'senior' },
  { id: 'user-3', email: 'analyst@rsqli.local', password: 'analyst123', clearance: 1, name: 'Analyst', role: 'analyst' },
];

const findings = [
  {
    id: 'finding-1',
    title: 'Unpatched Apache Server in Production',
    status: 'open',
    severity: 4,
    department: 'engineering',
    classification: 'C1',
    assignee: 'ops-team@rsqli.local',
    details: 'Server apache-01.prod has been running version 2.4.41 since March 2025. Multiple CVEs affect this version including CVE-2025-1234 (RCE) and CVE-2025-5678 (LFI). Patch to 2.4.62 or later is required.',
    createdAt: '2026-01-10T08:00:00Z'
  },
  {
    id: 'finding-2',
    title: 'Weak Password Policy on VPN Gateway',
    status: 'open',
    severity: 3,
    department: 'security',
    classification: 'C1',
    assignee: 'security-team@rsqli.local',
    details: 'VPN gateway allows passwords with fewer than 12 characters and does not enforce MFA for internal network access.',
    createdAt: '2026-01-12T10:30:00Z'
  },
  {
    id: 'finding-3',
    title: 'Exposed S3 Bucket with Customer Data',
    status: 'closed',
    severity: 5,
    department: 'engineering',
    classification: 'C2',
    assignee: 'platform-team@rsqli.local',
    details: 'S3 bucket "analytics-data-prod" was publicly readable for 3 days in December 2025. Estimated 50K customer records may have been exposed.',
    createdAt: '2025-12-15T14:00:00Z'
  },
  {
    id: 'finding-4',
    title: 'Non-Compliant Encryption at Rest',
    status: 'investigating',
    severity: 3,
    department: 'security',
    classification: 'C2',
    assignee: 'cloud-team@rsqli.local',
    details: 'RDS instances in us-west-2 do not use AWS KMS managed keys. Current encryption uses AES-256 with AWS managed keys.',
    createdAt: '2026-01-18T09:15:00Z'
  },
  {
    id: 'finding-5',
    title: 'Deprecated TLS Versions on API Gateway',
    status: 'open',
    severity: 2,
    department: 'engineering',
    classification: 'C1',
    assignee: 'api-team@rsqli.local',
    details: 'API Gateway still accepts TLS 1.0 and 1.1 connections from legacy clients.',
    createdAt: '2026-02-01T11:00:00Z'
  },
  {
    id: 'finding-6',
    title: 'Privileged Access Management Review',
    status: 'open',
    severity: 4,
    department: 'compliance',
    classification: 'C2',
    assignee: 'compliance-team@rsqli.local',
    details: 'Quarterly PAM review identified 23 service accounts with excessive privileges.',
    createdAt: '2026-02-05T16:00:00Z'
  },
  {
    id: 'finding-7',
    title: 'Executive Security Briefing Q1 2026',
    status: 'open',
    severity: 5,
    department: 'executive',
    classification: 'C3',
    assignee: 'cso@rsqli.local',
    details: 'No details',
    createdAt: '2026-02-10T07:00:00Z'
  },
  {
    id: 'finding-8',
    title: 'LDAP Injection Vulnerability in Internal Tool',
    status: 'open',
    severity: 3,
    department: 'engineering',
    classification: 'C1',
    assignee: 'internal-tools@rsqli.local',
    details: 'Internal employee directory search tool is vulnerable to LDAP injection.',
    createdAt: '2026-02-12T13:45:00Z'
  },
  {
    id: 'finding-9',
    title: 'Cross-Tenant Data Leakage Risk',
    status: 'closed',
    severity: 5,
    department: 'engineering',
    classification: 'C3',
    assignee: 'platform-team@rsqli.local',
    details: 'Database sharding misconfiguration allowed tenant A to access tenant B data.',
    createdAt: '2026-01-20T10:00:00Z'
  },
  {
    id: 'finding-10',
    title: 'Missing Audit Logs in Critical Systems',
    status: 'open',
    severity: 2,
    department: 'compliance',
    classification: 'C1',
    assignee: 'infra-team@rsqli.local',
    details: 'Systems in the PCI scope are not shipping audit logs to the central SIEM.',
    createdAt: '2026-02-15T08:30:00Z'
  },
];

module.exports = { users, findings };
