'use client'

import { useEffect, useState } from 'react'
import { getDevices, getActivities, getNotifications } from '@/app/actions/devices'
import { DashboardLayout } from './layout/dashboard-layout'
import { StatsGrid } from './dashboard/stats-grid'
import { DevicesList } from './dashboard/devices-list'
import { ActivityFeed } from './dashboard/activity-feed'
import { NotificationCenter } from './dashboard/notification-center'

interface User {
  id: string
  name: string | null
  email: string
}

export function DashboardClient({ user }: { user: User }) {
  const [devices, setDevices] = useState<any[]>([])
  const [activities, setActivities] = useState<any[]>([])
  const [notifications, setNotifications] = useState<any[]>([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const loadData = async () => {
      try {
        setLoading(true)
        const [devicesData, activitiesData, notificationsData] = await Promise.all([
          getDevices(),
          getActivities(undefined, 10),
          getNotifications(),
        ])
        setDevices(devicesData)
        setActivities(activitiesData)
        setNotifications(notificationsData)
      } catch (error) {
        console.error('Failed to load dashboard data:', error)
      } finally {
        setLoading(false)
      }
    }

    loadData()
  }, [])

  const unreadCount = notifications.filter((n) => !n.isRead).length

  return (
    <DashboardLayout user={user} unreadCount={unreadCount}>
      <div className="space-y-8">
        <div>
          <h1 className="text-3xl font-bold tracking-tight text-foreground">Dashboard</h1>
          <p className="text-sm text-muted-foreground mt-2">
            Welcome back, {user.name || user.email}
          </p>
        </div>

        {loading ? (
          <div className="flex items-center justify-center py-12">
            <div className="text-center space-y-2">
              <div className="w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto"></div>
              <p className="text-muted-foreground">Loading dashboard...</p>
            </div>
          </div>
        ) : (
          <div className="space-y-8">
            <StatsGrid devices={devices} activities={activities} />

            <div className="grid gap-8 lg:grid-cols-3">
              <div className="lg:col-span-2 space-y-8">
                <DevicesList devices={devices} />
                <ActivityFeed activities={activities} />
              </div>
              <NotificationCenter notifications={notifications} />
            </div>
          </div>
        )}
      </div>
    </DashboardLayout>
  )
}
