'use client'

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
  ChevronRight,
} from 'lucide-react'
import Link from 'next/link'
import { Button } from '@/components/ui/button'

interface Activity {
  id: string
  type: string
  description?: string
  createdAt: Date | string
  deviceId: string
}

interface ActivityFeedProps {
  activities: Activity[]
}

export function ActivityFeed({ activities }: ActivityFeedProps) {
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
    const now = new Date()
    const diff = now.getTime() - d.getTime()
    const minutes = Math.floor(diff / 60000)
    const hours = Math.floor(diff / 3600000)

    if (minutes < 1) return 'Just now'
    if (minutes < 60) return `${minutes}m ago`
    if (hours < 24) return `${hours}h ago`
    return d.toLocaleDateString()
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
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h2 className="text-xl font-bold text-foreground">Recent Activities</h2>
        <Link href="/activities">
          <Button variant="ghost" size="sm">
            View All
            <ChevronRight className="w-4 h-4 ml-1" />
          </Button>
        </Link>
      </div>

      {activities.length === 0 ? (
        <Card className="p-8 text-center">
          <Activity className="w-12 h-12 text-muted-foreground mx-auto mb-3 opacity-50" />
          <p className="text-muted-foreground">No activities yet</p>
        </Card>
      ) : (
        <Card>
          <div className="divide-y divide-border">
            {activities.slice(0, 8).map((activity) => (
              <div key={activity.id} className="p-4 hover:bg-muted/50 transition-colors">
                <div className="flex items-start gap-4">
                  <div className="p-2 bg-muted rounded-lg mt-1">
                    {getActivityIcon(activity.type)}
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className="font-medium text-foreground">
                      {getActivityLabel(activity.type)}
                    </p>
                    {activity.description && (
                      <p className="text-sm text-muted-foreground truncate">
                        {activity.description}
                      </p>
                    )}
                    <p className="text-xs text-muted-foreground mt-1">
                      {formatDate(activity.createdAt)}
                    </p>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </Card>
      )}
    </div>
  )
}
