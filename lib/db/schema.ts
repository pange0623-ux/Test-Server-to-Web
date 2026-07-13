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
  name: text('name').notNull(),
  deviceId: text('deviceId'),
  deviceType: text('deviceType'),
  osType: text('osType'),
  osVersion: text('osVersion'),
  appVersion: text('appVersion'),
  status: text('status').notNull().default('active'),
  lastSeen: timestamp('lastSeen'),
  isLocked: boolean('isLocked').notNull().default(false),
  location: text('location'),
  createdAt: timestamp('createdAt').notNull().defaultNow(),
  updatedAt: timestamp('updatedAt').notNull().defaultNow(),
})

export const activities = pgTable('activities', {
  id: text('id').primaryKey(),
  userId: text('userId').notNull(),
  deviceId: text('deviceId').notNull(),
  type: text('type').notNull(),
  description: text('description'),
  data: jsonb('data'),
  createdAt: timestamp('createdAt').notNull().defaultNow(),
})

export const notifications = pgTable('notifications', {
  id: text('id').primaryKey(),
  userId: text('userId').notNull(),
  deviceId: text('deviceId'),
  title: text('title').notNull(),
  message: text('message'),
  type: text('type'),
  isRead: boolean('isRead').notNull().default(false),
  data: jsonb('data'),
  createdAt: timestamp('createdAt').notNull().defaultNow(),
  readAt: timestamp('readAt'),
})

export const locations = pgTable('locations', {
  id: text('id').primaryKey(),
  userId: text('userId').notNull(),
  deviceId: text('deviceId').notNull(),
  latitude: numeric('latitude', { precision: 10, scale: 8 }),
  longitude: numeric('longitude', { precision: 11, scale: 8 }),
  accuracy: numeric('accuracy', { precision: 8, scale: 2 }),
  address: text('address'),
  createdAt: timestamp('createdAt').notNull().defaultNow(),
})
