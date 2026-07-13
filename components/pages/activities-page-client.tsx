'use client'

import { useEffect, useState } from 'react'
import { getActivities } from '@/app/actions/devices'
import { DashboardLayout } from '@/components/layout/dashboard-layout'
import { Card } from '@/components/ui/card'
import {
  Activity,
  Lock,
  Unlock,
  Eye,
  Volume2,
  Camera,
  Battery,
  Network,
} from 'lucide-react'

interface User {
  id: string
  name: string | null
  email: string
}

export function ActivitiesPageClient({ user }: { user: User }) {
  const [activities, setActivities] = useState<any[]>([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    loadActivities()
  }, [])

  const loadActivities = async () => {
    try {
      setLoading(true)
      const data = await getActivities(undefined, 100)
      setActivities(data)
    } catch (error) {
      console.error('Failed to load activities:', error)
    } finally {
      setLoading(false)
    }
  }

  const getActivityIcon = (type: string) => {
    switch (type) {
      case 'lock':
        return <Lock className="w-4 h-4 text-red-500" />
      case 'unlock':
        return <Unlock className="w-4 h-4 text-green-500" />
      case 'view':
        return <Eye className="w-4 h-4 text-blue-500" />
      case 'sound':
        return <Volume2 className="w-4 h-4 text-yellow-500" />
      case 'camera':
        return <Camera className="w-4 h-4 text-purple-500" />
      case 'battery':
        return <Battery className="w-4 h-4 text-orange-500" />
      case 'network':
        return <Network className="w-4 h-4 text-cyan-500" />
      default:
        return <Activity className="w-4 h-4 text-gray-500" />
    }
  }

  const formatDate = (date: Date | string) => {
    const d = new Date(date)
    return d.toLocaleString()
  }

  const getActivityLabel = (type: string) => {
    const labels: Record<string, string> = {
      lock: 'Device Locked',
      unlock: 'Device Unlocked',
      view: 'Device Viewed',
      sound: 'Sound Played',
      camera: 'Camera Accessed',
      battery: 'Battery Low',
      network: 'Network Changed',
    }
    return labels[type] || type.charAt(0).toUpperCase() + type.slice(1)
  }

  return (
    <DashboardLayout user={user}>
      <div className="space-y-6">
        <div>
          <h1 className="text-3xl font-bold tracking-tight text-foreground">
            All Activities
          </h1>
          <p className="text-sm text-muted-foreground mt-2">
            Complete log of all device activities and events
          </p>
        </div>

        {loading ? (
          <div className="flex items-center justify-center py-12">
            <div className="text-center space-y-2">
              <div className="w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto"></div>
              <p className="text-muted-foreground">Loading activities...</p>
            </div>
          </div>
        ) : activities.length === 0 ? (
          <Card className="p-8 text-center">
            <Activity className="w-12 h-12 text-muted-foreground mx-auto mb-3 opacity-50" />
            <p className="text-muted-foreground">No activities recorded yet</p>
          </Card>
        ) : (
          <Card>
            <div className="divide-y divide-border">
              {activities.map((activity) => (
                <div
                  key={activity.id}
                  className="p-4 hover:bg-muted/50 transition-colors"
                >
                  <div className="flex items-start gap-4">
                    <div className="p-2 bg-muted rounded-lg mt-1">
                      {getActivityIcon(activity.type)}
                    </div>
                    <div className="flex-1 min-w-0">
                      <div className="flex items-center justify-between gap-4">
                        <div>
                          <p className="font-medium text-foreground">
                            {getActivityLabel(activity.type)}
                          </p>
                          {activity.description && (
                            <p className="text-sm text-muted-foreground truncate mt-1">
                              {activity.description}
                            </p>
                          )}
                        </div>
                        <p className="text-sm text-muted-foreground whitespace-nowrap">
                          {formatDate(activity.createdAt)}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </Card>
        )}
      </div>
    </DashboardLayout>
  )
}
