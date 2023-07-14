import React from 'react';
import './Footer.css';


const Footer = () => {
  return (
    <footer>
      <p>
        <a
          href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev"
          rel="noopener noreferrer"
          target="_blank"
        >
          WebVulnLab
        </a>{' '}
        by{' '}
        <a
          href="https://github.com/sil3ntH4ck3r"
          rel="noopener noreferrer"
          target="_blank"
        >
          sil3nth4ck3r
        </a>{' '}
        is licensed under{' '}
        <a
          href="http://creativecommons.org/licenses/by-nc-sa/4.0/"
          rel="license noopener noreferrer"
          target="_blank"
        >
          CC BY-NC-SA 4.0
        </a>
      </p>
    </footer>
  );
};

export default Footer;