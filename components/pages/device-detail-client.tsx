'use client'

import { useEffect, useState } from 'react'
import { useRouter } from 'next/navigation'
import Link from 'next/link'
import {
  getDeviceById,
  updateDevice,
  deleteDevice,
  getActivities,
  getLocations,
} from '@/app/actions/devices'
import { DashboardLayout } from '@/components/layout/dashboard-layout'
import { Button } from '@/components/ui/button'
import { Card } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  ArrowLeft,
  Smartphone,
  Lock,
  Unlock,
  Trash2,
  MapPin,
  ActivitySquare,
  Edit2,
  X,
} from 'lucide-react'

interface User {
  id: string
  name: string | null
  email: string
}

export function DeviceDetailClient({
  deviceId,
  user,
}: {
  deviceId: string
  user: User
}) {
  const router = useRouter()
  const [device, setDevice] = useState<any>(null)
  const [activities, setActivities] = useState<any[]>([])
  const [locations, setLocations] = useState<any[]>([])
  const [loading, setLoading] = useState(true)
  const [editing, setEditing] = useState(false)
  const [editName, setEditName] = useState('')
  const [deleting, setDeleting] = useState(false)
  const [updatingLock, setUpdatingLock] = useState(false)

  useEffect(() => {
    loadData()
  }, [deviceId])

  const loadData = async () => {
    try {
      setLoading(true)
      const [deviceData, activitiesData, locationsData] = await Promise.all([
        getDeviceById(deviceId),
        getActivities(deviceId, 20),
        getLocations(deviceId, 10),
      ])
      setDevice(deviceData)
      setActivities(activitiesData)
      setLocations(locationsData)
      if (deviceData) {
        setEditName(deviceData.name)
      }
    } catch (error) {
      console.error('Failed to load device details:', error)
    } finally {
      setLoading(false)
    }
  }

  const handleSaveName = async () => {
    if (!device || !editName.trim()) return
    try {
      await updateDevice(deviceId, { name: editName })
      setDevice({ ...device, name: editName })
      setEditing(false)
    } catch (error) {
      console.error('Failed to update device:', error)
    }
  }

  const handleToggleLock = async () => {
    if (!device) return
    try {
      setUpdatingLock(true)
      const newLocked = !device.isLocked
      await updateDevice(deviceId, { isLocked: newLocked })
      setDevice({ ...device, isLocked: newLocked })
    } catch (error) {
      console.error('Failed to update lock status:', error)
    } finally {
      setUpdatingLock(false)
    }
  }

  const handleDelete = async () => {
    if (!confirm('Are you sure you want to delete this device?')) return
    try {
      setDeleting(true)
      await deleteDevice(deviceId)
      router.push('/devices')
    } catch (error) {
      console.error('Failed to delete device:', error)
    } finally {
      setDeleting(false)
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

  if (loading) {
    return (
      <DashboardLayout user={user}>
        <div className="flex items-center justify-center py-12">
          <div className="text-center space-y-2">
            <div className="w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto"></div>
            <p className="text-muted-foreground">Loading device details...</p>
          </div>
        </div>
      </DashboardLayout>
    )
  }

  if (!device) {
    return (
      <DashboardLayout user={user}>
        <div className="space-y-4">
          <Link href="/devices">
            <Button variant="ghost">
              <ArrowLeft className="w-4 h-4 mr-2" />
              Back to Devices
            </Button>
          </Link>
          <Card className="p-8 text-center">
            <p className="text-muted-foreground">Device not found</p>
          </Card>
        </div>
      </DashboardLayout>
    )
  }

  return (
    <DashboardLayout user={user}>
      <div className="space-y-8">
        <div className="flex items-center gap-4">
          <Link href="/devices">
            <Button variant="ghost" size="icon">
              <ArrowLeft className="w-4 h-4" />
            </Button>
          </Link>
          <div className="flex-1">
            {editing ? (
              <div className="flex items-center gap-2">
                <Input
                  value={editName}
                  onChange={(e) => setEditName(e.target.value)}
                  className="text-2xl font-bold"
                />
                <Button
                  size="sm"
                  onClick={handleSaveName}
                  disabled={!editName.trim()}
                >
                  Save
                </Button>
                <Button
                  size="sm"
                  variant="outline"
                  onClick={() => {
                    setEditing(false)
                    setEditName(device.name)
                  }}
                >
                  Cancel
                </Button>
              </div>
            ) : (
              <div className="flex items-center gap-4">
                <h1 className="text-3xl font-bold tracking-tight text-foreground">
                  {device.name}
                </h1>
                <Button
                  variant="ghost"
                  size="icon"
                  onClick={() => setEditing(true)}
                >
                  <Edit2 className="w-4 h-4" />
                </Button>
              </div>
            )}
          </div>
          <Button
            variant="destructive"
            onClick={handleDelete}
            disabled={deleting}
          >
            <Trash2 className="w-4 h-4 mr-2" />
            Delete
          </Button>
        </div>

        {/* Device Info Grid */}
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
          <Card className="p-4">
            <p className="text-sm text-muted-foreground">Device Type</p>
            <p className="text-lg font-semibold text-foreground mt-1">
              {device.deviceType || 'N/A'}
            </p>
          </Card>
          <Card className="p-4">
            <p className="text-sm text-muted-foreground">Operating System</p>
            <p className="text-lg font-semibold text-foreground mt-1">
              {device.osType || 'N/A'}
              {device.osVersion && ` (${device.osVersion})`}
            </p>
          </Card>
          <Card className="p-4">
            <p className="text-sm text-muted-foreground">Status</p>
            <p className="text-lg font-semibold text-foreground mt-1 capitalize">
              {device.status}
            </p>
          </Card>
          <Card className="p-4">
            <p className="text-sm text-muted-foreground">Last Seen</p>
            <p className="text-lg font-semibold text-foreground mt-1">
              {formatDate(device.lastSeen)}
            </p>
          </Card>
        </Card>

        {/* Device Actions */}
        <Card className="p-6">
          <h2 className="text-lg font-semibold mb-4">Device Actions</h2>
          <div className="flex gap-4">
            <Button
              onClick={handleToggleLock}
              disabled={updatingLock}
              variant={device.isLocked ? 'default' : 'outline'}
            >
              {device.isLocked ? (
                <>
                  <Unlock className="w-4 h-4 mr-2" />
                  Unlock Device
                </>
              ) : (
                <>
                  <Lock className="w-4 h-4 mr-2" />
                  Lock Device
                </>
              )}
            </Button>
          </div>
        </Card>

        {/* Recent Activities */}
        <Card className="p-6">
          <h2 className="text-lg font-semibold mb-4 flex items-center gap-2">
            <ActivitySquare className="w-5 h-5" />
            Recent Activities
          </h2>
          {activities.length === 0 ? (
            <p className="text-muted-foreground text-center py-8">No activities recorded</p>
          ) : (
            <div className="space-y-3 divide-y divide-border">
              {activities.map((activity) => (
                <div key={activity.id} className="py-3 first:pt-0 last:pb-0">
                  <p className="font-medium text-foreground">{activity.type}</p>
                  {activity.description && (
                    <p className="text-sm text-muted-foreground mt-1">
                      {activity.description}
                    </p>
                  )}
                  <p className="text-xs text-muted-foreground mt-2">
                    {formatDate(activity.createdAt)}
                  </p>
                </div>
              ))}
            </div>
          )}
        </Card>

        {/* Locations */}
        {locations.length > 0 && (
          <Card className="p-6">
            <h2 className="text-lg font-semibold mb-4 flex items-center gap-2">
              <MapPin className="w-5 h-5" />
              Location History
            </h2>
            <div className="space-y-3 divide-y divide-border">
              {locations.slice(0, 10).map((location) => (
                <div key={location.id} className="py-3 first:pt-0 last:pb-0">
                  <p className="font-medium text-foreground">
                    {location.address || 'Unknown Location'}
                  </p>
                  {location.latitude && location.longitude && (
                    <p className="text-sm text-muted-foreground mt-1">
                      {location.latitude}, {location.longitude}
                      {location.accuracy && ` (±${location.accuracy}m)`}
                    </p>
                  )}
                  <p className="text-xs text-muted-foreground mt-2">
                    {formatDate(location.createdAt)}
                  </p>
                </div>
              ))}
            </div>
          </Card>
        )}
      </div>
    </DashboardLayout>
  )
}
