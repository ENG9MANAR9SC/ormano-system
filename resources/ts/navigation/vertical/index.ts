import type { VerticalNavItems } from '@/@layouts/types'

export default [
  {
    title: 'Home',
    to: { name: 'index' },
    icon: { icon: 'tabler-smart-home' },
  },
  {
    to: {name: 'user'},
    icon: {icon: 'tabler-user'},
    title: 'Patients',
  },
  {
    to: {name: 'appointment'},
    icon: {icon:'tabler-calendar-check'},
    title: 'Appointments',
  },
  {
    to: {name: 'invoice'},
    icon: {icon:'tabler-file-text'},
    title: 'Invoices',
  },
  {
    to: {name: 'payment'},
    icon: {icon:'tabler-file-arrow-left'},
    title: 'Payments',
  },
  {
    to: {name: 'expense'},
    icon: {icon:'tabler-file-arrow-right'},
    title: 'Expenses',
  },
  { heading: 'Settings' },

  {
    title: 'Categories',
    to: { name: 'category' },
    icon: {icon: 'tabler-packages'},
  },
  {
    icon: {icon:'tabler-device-mobile'},
    to: { name: 'device' },
    title: 'Devices',
  },
  {
    to: {name:'referral'},
    icon:{icon: 'tabler-users'},
    title: 'Referrals',
  },
  {
    children: [
      {
        to: {name: 'admin'},
        title: 'Team',
      },
      {
        to: {name: 'role'},
        title: 'Roles',
      },
    ],
    icon: {icon:'tabler-users'},
    title: 'Employees',
  },
  {
    children: [
      {
        to: {name:'report-general'},
        title: 'General',
      },
      {
        to: {name: 'report-financial'},
        title: 'Financial',
      },
    ],
    icon: {icon: 'tabler-files'},
    title: 'Reports',
  },

] as VerticalNavItems
