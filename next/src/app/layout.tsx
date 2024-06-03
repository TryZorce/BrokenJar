import React, { ReactNode } from 'react';
import './globals.css';

export const metadata = {
  title: 'BrokenPot',
  description: 'Site de de remboursement de pot',
};

interface LayoutProps {
  children: ReactNode;
}

const Layout = ({ children }: LayoutProps) => {
  return (
    <html lang="fr">
      <head>
        <title>{metadata.title}</title>
        <meta name="description" content={metadata.description} />
      </head>
      <body>
        <main>{children}</main>
      </body>
    </html>
  );
};

export default Layout;
