const KNOWN_OPERATORS = ['==', '!=', '=gt=', '=lt=', '=in=', '=re='];

function parseRSQL(query) {
  if (!query || query.trim() === '') {
    return () => true;
  }

  const conditions = [];
  const orGroups = query.split(',');

  for (const orGroup of orGroups) {
    const andConditions = orGroup.split(';').map(s => s.trim()).filter(Boolean);
    const parsedAnd = andConditions.map(cond => parseCondition(cond));
    conditions.push(parsedAnd);
  }

  return function (item) {
    try {
      return conditions.some(group =>
        group.every(fn => fn(item))
      );
    } catch {
      return false;
    }
  };
}

function parseCondition(cond) {
  const operators = [
    { op: '=re=', regex: /^(.+?)=re=(.+)$/ },
    { op: '=in=', regex: /^(.+?)=in=\((.+)\)$/ },
    { op: '=gt=', regex: /^(.+?)=gt=(.+)$/ },
    { op: '=lt=', regex: /^(.+?)=lt=(.+)$/ },
    { op: '==', regex: /^(.+?)==(.+)$/ },
    { op: '!=', regex: /^(.+?)!=(.+)$/ },
  ];

  for (const { op, regex } of operators) {
    const match = cond.match(regex);
    if (match) {
      const field = match[1].trim();
      const rawValue = match[2].trim();

      switch (op) {
        case '==':
          if (rawValue.includes('*')) {
            const pattern = '^' + rawValue.replace(/[.+?^${}()|[\]\\]/g, '\\$&').replace(/\*/g, '.*') + '$';
            try {
              const re = new RegExp(pattern);
              return item => re.test(String(item[field]));
            } catch {
              return () => false;
            }
          }
          return item => String(item[field]) === rawValue;
        case '!=':
          return item => String(item[field]) !== rawValue;
        case '=gt=':
          return item => parseFloat(item[field]) > parseFloat(rawValue);
        case '=lt=':
          return item => parseFloat(item[field]) < parseFloat(rawValue);
        case '=in=':
          return item => rawValue.split('|').map(s => s.trim()).includes(String(item[field]));
        case '=re=':
          try {
            const re = new RegExp(rawValue);
            return item => re.test(String(item[field]));
          } catch {
            return () => false;
          }
      }
    }
  }

  const err = new Error(`Invalid RSQL condition: "${cond}". Supported operators: ${KNOWN_OPERATORS.join(', ')}`);
  err.statusCode = 400;
  throw err;
}

module.exports = { parseRSQL };
