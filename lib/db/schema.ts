import { pgTable, text, timestamp, boolean, integer, numeric, varchar, jsonb } from 'drizzle-orm/pg-core'

// --- Better Auth required tables -------------------------------------------
// Column names are camelCase to match Better Auth's defaults. Do not rename.

export const user = pgTable('user', {
  id: text('id').primaryKey(),
  name: text('name').notNull(),
  email: text('email').notNull().unique(),
  emailVerified: boolean('emailVerified').notNull().default(false),
  image: text('image'),
  createdAt: timestamp('createdAt').notNull().defaultNow(),
  updatedAt: timestamp('updatedAt').notNull().defaultNow(),
})

export const session = pgTable('session', {
  id: text('id').primaryKey(),
  expiresAt: timestamp('expiresAt').notNull(),
  token: text('token').notNull().unique(),
  createdAt: timestamp('createdAt').notNull().defaultNow(),
  updatedAt: timestamp('updatedAt').notNull().defaultNow(),
  ipAddress: text('ipAddress'),
  userAgent: text('userAgent'),
  userId: text('userId')
    .notNull()
    .references(() => user.id, { onDelete: 'cascade' }),
})

export const account = pgTable('account', {
  id: text('id').primaryKey(),
  accountId: text('accountId').notNull(),
  providerId: text('providerId').notNull(),
  userId: text('userId')
    .notNull()
    .references(() => user.id, { onDelete: 'cascade' }),
  accessToken: text('accessToken'),
  refreshToken: text('refreshToken'),
  idToken: text('idToken'),
  accessTokenExpiresAt: timestamp('accessTokenExpiresAt'),
  refreshTokenExpiresAt: timestamp('refreshTokenExpiresAt'),
  scope: text('scope'),
  password: text('password'),
  createdAt: timestamp('createdAt').notNull().defaultNow(),
  updatedAt: timestamp('updatedAt').notNull().defaultNow(),
})

export const verification = pgTable('verification', {
  id: text('id').primaryKey(),
  identifier: text('identifier').notNull(),
  value: text('value').notNull(),
  expiresAt: timestamp('expiresAt').notNull(),
  createdAt: timestamp('createdAt').defaultNow(),
  updatedAt: timestamp('updatedAt').defaultNow(),
})

// --- App tables: Device Tracking System -----------------------------------

export const devices = pgTable('devices', {
  id: text('id').primaryKey(),
  userId: text('userId').notNull(),
  deviceName: text('deviceName').notNull(),
  deviceType: varchar('deviceType', { length: 50 }).notNull(), // 'phone', 'tablet', 'smartwatch'
  imei: text('imei'),
  phoneNumber: text('phoneNumber'),
  osType: varchar('osType', { length: 50 }).notNull(), // 'ios', 'android'
  osVersion: text('osVersion'),
  status: varchar('status', { length: 20 }).notNull().default('active'), // 'active', 'inactive', 'lost', 'offline'
  lastSeenAt: timestamp('lastSeenAt'),
  latitude: numeric('latitude', { precision: 10, scale: 8 }),
  longitude: numeric('longitude', { precision: 11, scale: 8 }),
  battery: integer('battery'), // 0-100
  isCharging: boolean('isCharging').default(false),
  storageUsed: integer('storageUsed'), // in MB
  storageTotal: integer('storageTotal'), // in MB
  createdAt: timestamp('createdAt').notNull().defaultNow(),
  updatedAt: timestamp('updatedAt').notNull().defaultNow(),
})

export const activities = pgTable('activities', {
  id: text('id').primaryKey(),
  userId: text('userId').notNull(),
  deviceId: text('deviceId').notNull(),
  activityType: varchar('activityType', { length: 50 }).notNull(), // 'location_change', 'app_install', 'app_uninstall', 'call', 'sms', 'web_visit', 'device_restart', etc.
  description: text('description'),
  appName: text('appName'),
  contactName: text('contactName'),
  phoneNumber: text('phoneNumber'),
  webUrl: text('webUrl'),
  latitude: numeric('latitude', { precision: 10, scale: 8 }),
  longitude: numeric('longitude', { precision: 11, scale: 8 }),
  metadata: jsonb('metadata'), // Store additional data as needed
  createdAt: timestamp('createdAt').notNull().defaultNow(),
})

export const notifications = pgTable('notifications', {
  id: text('id').primaryKey(),
  userId: text('userId').notNull(),
  deviceId: text('deviceId'),
  type: varchar('type', { length: 50 }).notNull(), // 'device_offline', 'low_battery', 'location_alert', 'app_alert', etc.
  title: text('title').notNull(),
  message: text('message').notNull(),
  isRead: boolean('isRead').notNull().default(false),
  actionUrl: text('actionUrl'),
  createdAt: timestamp('createdAt').notNull().defaultNow(),
})

export const geofences = pgTable('geofences', {
  id: text('id').primaryKey(),
  userId: text('userId').notNull(),
  deviceId: text('deviceId'),
  name: text('name').notNull(),
  latitude: numeric('latitude', { precision: 10, scale: 8 }).notNull(),
  longitude: numeric('longitude', { precision: 11, scale: 8 }).notNull(),
  radius: integer('radius').notNull(), // in meters
  isActive: boolean('isActive').notNull().default(true),
  notifyOnEnter: boolean('notifyOnEnter').notNull().default(true),
  notifyOnExit: boolean('notifyOnExit').notNull().default(true),
  createdAt: timestamp('createdAt').notNull().defaultNow(),
  updatedAt: timestamp('updatedAt').notNull().defaultNow(),
})

export const appBlacklist = pgTable('app_blacklist', {
  id: text('id').primaryKey(),
  userId: text('userId').notNull(),
  appName: text('appName').notNull(),
  appPackage: text('appPackage'),
  reason: text('reason'),
  createdAt: timestamp('createdAt').notNull().defaultNow(),
})

export const screenTimeSettings = pgTable('screen_time_settings', {
  id: text('id').primaryKey(),
  userId: text('userId').notNull(),
  deviceId: text('deviceId'),
  maxDailyScreenTime: integer('maxDailyScreenTime'), // in minutes
  bedtimeStart: text('bedtimeStart'), // HH:mm format
  bedtimeEnd: text('bedtimeEnd'), // HH:mm format
  createdAt: timestamp('createdAt').notNull().defaultNow(),
  updatedAt: timestamp('updatedAt').notNull().defaultNow(),
})

export const userSettings = pgTable('user_settings', {
  id: text('id').primaryKey(),
  userId: text('userId').notNull().unique(),
  theme: varchar('theme', { length: 20 }).default('dark'), // 'light', 'dark'
  emailNotifications: boolean('emailNotifications').notNull().default(true),
  pushNotifications: boolean('pushNotifications').notNull().default(true),
  dataRefreshInterval: integer('dataRefreshInterval').default(30), // in seconds
  createdAt: timestamp('createdAt').notNull().defaultNow(),
  updatedAt: timestamp('updatedAt').notNull().defaultNow(),
})
