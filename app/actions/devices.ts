'use server'

import { auth } from '@/lib/auth'
import { db } from '@/lib/db'
import { devices, activities, locations, notifications } from '@/lib/db/schema'
import { and, desc, eq } from 'drizzle-orm'
import { headers } from 'next/headers'
import { revalidatePath } from 'next/cache'
import { nanoid } from 'nanoid'

async function getUserId() {
  const session = await auth.api.getSession({ headers: await headers() })
  if (!session?.user) throw new Error('Unauthorized')
  return session.user.id
}

export async function getDevices() {
  const userId = await getUserId()
  return db
    .select()
    .from(devices)
    .where(eq(devices.userId, userId))
    .orderBy(desc(devices.updatedAt))
}

export async function getDeviceById(deviceId: string) {
  const userId = await getUserId()
  const result = await db
    .select()
    .from(devices)
    .where(and(eq(devices.id, deviceId), eq(devices.userId, userId)))
    .limit(1)
  return result[0] || null
}

export async function addDevice(data: {
  name: string
  deviceId?: string
  deviceType?: string
  osType?: string
  osVersion?: string
  appVersion?: string
}) {
  const userId = await getUserId()
  const id = nanoid()
  
  await db.insert(devices).values({
    id,
    userId,
    name: data.name,
    deviceId: data.deviceId,
    deviceType: data.deviceType,
    osType: data.osType,
    osVersion: data.osVersion,
    appVersion: data.appVersion,
    status: 'active',
  })
  
  revalidatePath('/devices')
  return { id }
}

export async function updateDevice(deviceId: string, data: Partial<typeof devices.$inferInsert>) {
  const userId = await getUserId()
  
  await db
    .update(devices)
    .set({ ...data, updatedAt: new Date() })
    .where(and(eq(devices.id, deviceId), eq(devices.userId, userId)))
  
  revalidatePath('/devices')
  revalidatePath(`/devices/${deviceId}`)
}

export async function deleteDevice(deviceId: string) {
  const userId = await getUserId()
  
  await db
    .delete(devices)
    .where(and(eq(devices.id, deviceId), eq(devices.userId, userId)))
  
  revalidatePath('/devices')
}

export async function getActivities(deviceId?: string, limit: number = 50) {
  const userId = await getUserId()
  
  const whereCondition = deviceId
    ? and(eq(activities.userId, userId), eq(activities.deviceId, deviceId))
    : eq(activities.userId, userId)
  
  return db
    .select()
    .from(activities)
    .where(whereCondition)
    .orderBy(desc(activities.createdAt))
    .limit(limit)
}

export async function addActivity(data: {
  deviceId: string
  type: string
  description?: string
  data?: any
}) {
  const userId = await getUserId()
  const id = nanoid()
  
  await db.insert(activities).values({
    id,
    userId,
    deviceId: data.deviceId,
    type: data.type,
    description: data.description,
    data: data.data,
  })
  
  revalidatePath('/activities')
}

export async function getLocations(deviceId: string, limit: number = 100) {
  const userId = await getUserId()
  
  return db
    .select()
    .from(locations)
    .where(and(eq(locations.userId, userId), eq(locations.deviceId, deviceId)))
    .orderBy(desc(locations.createdAt))
    .limit(limit)
}

export async function addLocation(data: {
  deviceId: string
  latitude: number
  longitude: number
  accuracy?: number
  address?: string
}) {
  const userId = await getUserId()
  const id = nanoid()
  
  await db.insert(locations).values({
    id,
    userId,
    deviceId: data.deviceId,
    latitude: data.latitude.toString(),
    longitude: data.longitude.toString(),
    accuracy: data.accuracy?.toString(),
    address: data.address,
  })
  
  revalidatePath(`/devices/${data.deviceId}`)
}

export async function getNotifications(unreadOnly: boolean = false) {
  const userId = await getUserId()
  
  const whereCondition = unreadOnly
    ? and(eq(notifications.userId, userId), eq(notifications.isRead, false))
    : eq(notifications.userId, userId)
  
  return db
    .select()
    .from(notifications)
    .where(whereCondition)
    .orderBy(desc(notifications.createdAt))
    .limit(50)
}

export async function addNotification(data: {
  deviceId?: string
  title: string
  message?: string
  type?: string
  data?: any
}) {
  const userId = await getUserId()
  const id = nanoid()
  
  await db.insert(notifications).values({
    id,
    userId,
    deviceId: data.deviceId,
    title: data.title,
    message: data.message,
    type: data.type,
    data: data.data,
  })
  
  revalidatePath('/notifications')
}

export async function markNotificationAsRead(notificationId: string) {
  const userId = await getUserId()
  
  await db
    .update(notifications)
    .set({ isRead: true, readAt: new Date() })
    .where(and(eq(notifications.id, notificationId), eq(notifications.userId, userId)))
  
  revalidatePath('/notifications')
}
