'use client'

import { Card } from '@/components/ui/card'
import { Smartphone, ActivitySquare, AlertCircle, TrendingUp } from 'lucide-react'

interface StatsGridProps {
  devices: any[]
  activities: any[]
}

export function StatsGrid({ devices, activities }: StatsGridProps) {
  const activeDevices = devices.filter((d) => d.status === 'active').length
  const lockedDevices = devices.filter((d) => d.isLocked).length
  const totalActivities = activities.length

  const stats = [
    {
      label: 'Total Devices',
      value: devices.length,
      icon: Smartphone,
      color: 'text-blue-500',
      bgColor: 'bg-blue-50 dark:bg-blue-950',
    },
    {
      label: 'Active Devices',
      value: activeDevices,
      icon: TrendingUp,
      color: 'text-green-500',
      bgColor: 'bg-green-50 dark:bg-green-950',
    },
    {
      label: 'Locked Devices',
      value: lockedDevices,
      icon: AlertCircle,
      color: 'text-red-500',
      bgColor: 'bg-red-50 dark:bg-red-950',
    },
    {
      label: 'Recent Activities',
      value: totalActivities,
      icon: ActivitySquare,
      color: 'text-purple-500',
      bgColor: 'bg-purple-50 dark:bg-purple-950',
    },
  ]

  return (
    <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
      {stats.map((stat) => (
        <Card key={stat.label} className="p-6">
          <div className="flex items-center justify-between">
            <div className="space-y-2">
              <p className="text-sm font-medium text-muted-foreground">{stat.label}</p>
              <p className="text-3xl font-bold text-foreground">{stat.value}</p>
            </div>
            <div className={`p-3 rounded-lg ${stat.bgColor}`}>
              <stat.icon className={`w-6 h-6 ${stat.color}`} />
            </div>
          </div>
        </Card>
      ))}
    </div>
  )
}
