'use client'

import Link from 'next/link'
import { Card } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import {
  Smartphone,
  Laptop,
  Tablet,
  Watch,
  Lock,
  Unlock,
  ChevronRight,
} from 'lucide-react'

interface Device {
  id: string
  name: string
  deviceType?: string
  osType?: string
  status: string
  isLocked: boolean
  lastSeen?: Date | string
}

interface DevicesListProps {
  devices: Device[]
}

export function DevicesList({ devices }: DevicesListProps) {
  const getDeviceIcon = (type?: string) => {
    switch (type?.toLowerCase()) {
      case 'tablet':
        return <Tablet className="w-5 h-5" />
      case 'laptop':
        return <Laptop className="w-5 h-5" />
      case 'watch':
        return <Watch className="w-5 h-5" />
      default:
        return <Smartphone className="w-5 h-5" />
    }
  }

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'active':
        return 'bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-300'
      case 'inactive':
        return 'bg-gray-100 text-gray-800 dark:bg-gray-950 dark:text-gray-300'
      case 'offline':
        return 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300'
      default:
        return 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300'
    }
  }

  const formatDate = (date?: Date | string) => {
    if (!date) return 'Never'
    const d = new Date(date)
    const now = new Date()
    const diff = now.getTime() - d.getTime()
    const minutes = Math.floor(diff / 60000)
    const hours = Math.floor(diff / 3600000)
    const days = Math.floor(diff / 86400000)

    if (minutes < 1) return 'Just now'
    if (minutes < 60) return `${minutes}m ago`
    if (hours < 24) return `${hours}h ago`
    if (days < 7) return `${days}d ago`
    return d.toLocaleDateString()
  }

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h2 className="text-xl font-bold text-foreground">Your Devices</h2>
        <Link href="/devices">
          <Button variant="ghost" size="sm">
            View All
            <ChevronRight className="w-4 h-4 ml-1" />
          </Button>
        </Link>
      </div>

      {devices.length === 0 ? (
        <Card className="p-8 text-center">
          <Smartphone className="w-12 h-12 text-muted-foreground mx-auto mb-3 opacity-50" />
          <p className="text-muted-foreground mb-4">No devices yet</p>
          <Link href="/devices">
            <Button>Add a Device</Button>
          </Link>
        </Card>
      ) : (
        <div className="grid gap-4">
          {devices.slice(0, 5).map((device) => (
            <Card
              key={device.id}
              className="p-4 hover:bg-muted/50 transition-colors cursor-pointer"
            >
              <Link href={`/devices/${device.id}`}>
                <div className="flex items-start justify-between">
                  <div className="flex items-start gap-4 flex-1">
                    <div className="p-2 bg-muted rounded-lg">
                      {getDeviceIcon(device.deviceType)}
                    </div>
                    <div className="flex-1">
                      <h3 className="font-semibold text-foreground">{device.name}</h3>
                      <p className="text-sm text-muted-foreground mt-1">
                        {device.osType || 'Unknown OS'} • Last seen{' '}
                        {formatDate(device.lastSeen)}
                      </p>
                    </div>
                  </div>
                  <div className="flex items-center gap-2">
                    {device.isLocked && (
                      <div className="p-2 bg-red-100 dark:bg-red-950 rounded-lg">
                        <Lock className="w-4 h-4 text-red-600 dark:text-red-400" />
                      </div>
                    )}
                    <Badge className={getStatusColor(device.status)}>
                      {device.status}
                    </Badge>
                  </div>
                </div>
              </Link>
            </Card>
          ))}
        </div>
      )}
    </div>
  )
}
