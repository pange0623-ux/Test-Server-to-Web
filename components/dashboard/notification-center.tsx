'use client'

import { useState } from 'react'
import { Card } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import {
  Bell,
  AlertCircle,
  CheckCircle,
  Info,
  X,
} from 'lucide-react'
import { markNotificationAsRead } from '@/app/actions/devices'
import Link from 'next/link'

interface Notification {
  id: string
  title: string
  message?: string
  type?: string
  isRead: boolean
  createdAt: Date | string
}

interface NotificationCenterProps {
  notifications: Notification[]
}

export function NotificationCenter({ notifications }: NotificationCenterProps) {
  const [dismissedIds, setDismissedIds] = useState<Set<string>>(new Set())

  const getNotificationIcon = (type?: string) => {
    switch (type) {
      case 'warning':
        return <AlertCircle className="w-4 h-4 text-yellow-500" />
      case 'success':
        return <CheckCircle className="w-4 h-4 text-green-500" />
      case 'info':
        return <Info className="w-4 h-4 text-blue-500" />
      default:
        return <Bell className="w-4 h-4 text-gray-500" />
    }
  }

  const getNotificationBgColor = (type?: string, isRead?: boolean) => {
    if (isRead) return 'bg-transparent'
    switch (type) {
      case 'warning':
        return 'bg-yellow-50 dark:bg-yellow-950'
      case 'success':
        return 'bg-green-50 dark:bg-green-950'
      case 'info':
        return 'bg-blue-50 dark:bg-blue-950'
      default:
        return 'bg-muted'
    }
  }

  const formatDate = (date: Date | string) => {
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

  const handleDismiss = (id: string) => {
    setDismissedIds((prev) => new Set(prev).add(id))
    markNotificationAsRead(id).catch(console.error)
  }

  const visibleNotifications = notifications.filter(
    (n) => !dismissedIds.has(n.id)
  )

  const unreadCount = visibleNotifications.filter((n) => !n.isRead).length

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h2 className="text-xl font-bold text-foreground">Notifications</h2>
        {unreadCount > 0 && (
          <Badge variant="destructive">{unreadCount}</Badge>
        )}
      </div>

      {visibleNotifications.length === 0 ? (
        <Card className="p-8 text-center">
          <Bell className="w-12 h-12 text-muted-foreground mx-auto mb-3 opacity-50" />
          <p className="text-muted-foreground">All caught up!</p>
        </Card>
      ) : (
        <div className="space-y-2 max-h-96 overflow-y-auto">
          {visibleNotifications.map((notification) => (
            <div
              key={notification.id}
              className={`p-4 rounded-lg border border-border transition-colors ${getNotificationBgColor(
                notification.type,
                notification.isRead
              )}`}
            >
              <div className="flex items-start gap-3">
                <div className="mt-1">
                  {getNotificationIcon(notification.type)}
                </div>
                <div className="flex-1 min-w-0">
                  <p className="font-medium text-foreground text-sm">
                    {notification.title}
                  </p>
                  {notification.message && (
                    <p className="text-xs text-muted-foreground mt-1 line-clamp-2">
                      {notification.message}
                    </p>
                  )}
                  <p className="text-xs text-muted-foreground mt-2">
                    {formatDate(notification.createdAt)}
                  </p>
                </div>
                <button
                  onClick={() => handleDismiss(notification.id)}
                  className="text-muted-foreground hover:text-foreground transition-colors"
                >
                  <X className="w-4 h-4" />
                </button>
              </div>
            </div>
          ))}
        </div>
      )}

      <Link href="/notifications" className="block">
        <Button variant="outline" className="w-full" size="sm">
          View All Notifications
        </Button>
      </Link>
    </div>
  )
}
