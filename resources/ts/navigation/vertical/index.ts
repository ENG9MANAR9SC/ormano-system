import type { VerticalNavItems } from '@/@layouts/types'

export default [
  { heading: 'Dashboard' },
  {
    title: 'Home',
    to: { name: 'index' },
    icon: { icon: 'tabler-smart-home' },
  },
  {
    to: {name: 'appointment'},
    icon: {icon:'tabler-calendar-check'},
    title: 'Appointments',
  },
  {
    to: {name: 'user'},
    icon: {icon:'tabler-users'},
    title: 'Users',
  },
  {
    to: {name: 'case'},
    icon: {icon:'tabler-briefcase'},
    title: 'Cases',
  },

  { heading: 'Settings' },

  {
    children: [
      {
        to: {name:'report-general'},
        title: 'General',
      },
      {
        to: {name: 'country'},
        title: 'Countries',
      },
      {
        to: {name: 'currency'},
        title: 'Currencies',
      },
      {
        to: {name: 'court'},
        title: 'Courts',
      },
    ],
    icon: {icon: 'tabler-settings'},
    title: 'Settings',
  },
  {
    children: [
      {
        to: {name: 'admin'},
        title: 'Admin',
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
        to: {name:'order'},
        title: 'Orders',
      },
      {
        to: {name: 'payment'},
        title: 'Payments',
      },
      {
        to: {name: 'expense'},
        title: 'Expenses',
      },
    ],
    icon: {icon: 'tabler-brand-mastercard'},
    title: 'Financial',
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
