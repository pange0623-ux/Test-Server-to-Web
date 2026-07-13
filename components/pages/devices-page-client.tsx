'use client'

import { useEffect, useState } from 'react'
import Link from 'next/link'
import { getDevices, addDevice } from '@/app/actions/devices'
import { DashboardLayout } from '@/components/layout/dashboard-layout'
import { Button } from '@/components/ui/button'
import { Card } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Smartphone,
  Laptop,
  Tablet,
  Watch,
  Plus,
  Trash2,
  Lock,
  ChevronRight,
} from 'lucide-react'

interface User {
  id: string
  name: string | null
  email: string
}

export function DevicesPageClient({ user }: { user: User }) {
  const [devices, setDevices] = useState<any[]>([])
  const [loading, setLoading] = useState(true)
  const [showAddForm, setShowAddForm] = useState(false)
  const [formData, setFormData] = useState({
    name: '',
    deviceType: 'phone',
    osType: 'android',
  })
  const [submitting, setSubmitting] = useState(false)

  useEffect(() => {
    loadDevices()
  }, [])

  const loadDevices = async () => {
    try {
      setLoading(true)
      const data = await getDevices()
      setDevices(data)
    } catch (error) {
      console.error('Failed to load devices:', error)
    } finally {
      setLoading(false)
    }
  }

  const handleAddDevice = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!formData.name.trim()) return

    try {
      setSubmitting(true)
      await addDevice({
        name: formData.name,
        deviceType: formData.deviceType,
        osType: formData.osType,
      })
      setFormData({ name: '', deviceType: 'phone', osType: 'android' })
      setShowAddForm(false)
      await loadDevices()
    } catch (error) {
      console.error('Failed to add device:', error)
    } finally {
      setSubmitting(false)
    }
  }

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
      default:
        return 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300'
    }
  }

  return (
    <DashboardLayout user={user}>
      <div className="space-y-8">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold tracking-tight text-foreground">Devices</h1>
            <p className="text-sm text-muted-foreground mt-2">
              Manage and monitor all your tracked devices
            </p>
          </div>
          <Button onClick={() => setShowAddForm(!showAddForm)}>
            <Plus className="w-4 h-4 mr-2" />
            Add Device
          </Button>
        </div>

        {showAddForm && (
          <Card className="p-6">
            <h2 className="text-lg font-semibold mb-4">Add New Device</h2>
            <form onSubmit={handleAddDevice} className="space-y-4 max-w-sm">
              <div>
                <Label htmlFor="name">Device Name</Label>
                <Input
                  id="name"
                  placeholder="e.g., iPhone 14, Samsung Galaxy"
                  value={formData.name}
                  onChange={(e) =>
                    setFormData({ ...formData, name: e.target.value })
                  }
                  required
                />
              </div>

              <div>
                <Label htmlFor="deviceType">Device Type</Label>
                <select
                  id="deviceType"
                  value={formData.deviceType}
                  onChange={(e) =>
                    setFormData({ ...formData, deviceType: e.target.value })
                  }
                  className="w-full px-3 py-2 border border-border rounded-md bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary"
                >
                  <option value="phone">Phone</option>
                  <option value="tablet">Tablet</option>
                  <option value="laptop">Laptop</option>
                  <option value="watch">Watch</option>
                </select>
              </div>

              <div>
                <Label htmlFor="osType">Operating System</Label>
                <select
                  id="osType"
                  value={formData.osType}
                  onChange={(e) =>
                    setFormData({ ...formData, osType: e.target.value })
                  }
                  className="w-full px-3 py-2 border border-border rounded-md bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary"
                >
                  <option value="android">Android</option>
                  <option value="ios">iOS</option>
                  <option value="windows">Windows</option>
                  <option value="macos">macOS</option>
                  <option value="linux">Linux</option>
                </select>
              </div>

              <div className="flex gap-2">
                <Button type="submit" disabled={submitting}>
                  {submitting ? 'Adding...' : 'Add Device'}
                </Button>
                <Button
                  type="button"
                  variant="outline"
                  onClick={() => setShowAddForm(false)}
                >
                  Cancel
                </Button>
              </div>
            </form>
          </Card>
        )}

        {loading ? (
          <div className="flex items-center justify-center py-12">
            <div className="text-center space-y-2">
              <div className="w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto"></div>
              <p className="text-muted-foreground">Loading devices...</p>
            </div>
          </div>
        ) : devices.length === 0 ? (
          <Card className="p-8 text-center">
            <Smartphone className="w-12 h-12 text-muted-foreground mx-auto mb-3 opacity-50" />
            <p className="text-muted-foreground mb-4">No devices added yet</p>
            <Button onClick={() => setShowAddForm(true)}>Add Your First Device</Button>
          </Card>
        ) : (
          <div className="grid gap-4">
            {devices.map((device) => (
              <Link key={device.id} href={`/devices/${device.id}`}>
                <Card className="p-6 hover:bg-muted/50 transition-colors cursor-pointer">
                  <div className="flex items-start justify-between">
                    <div className="flex items-start gap-4 flex-1">
                      <div className="p-3 bg-muted rounded-lg">
                        {getDeviceIcon(device.deviceType)}
                      </div>
                      <div className="flex-1">
                        <h3 className="text-lg font-semibold text-foreground">
                          {device.name}
                        </h3>
                        <p className="text-sm text-muted-foreground mt-1">
                          {device.osType || 'Unknown OS'}
                          {device.osVersion && ` • ${device.osVersion}`}
                        </p>
                        {device.appVersion && (
                          <p className="text-xs text-muted-foreground mt-1">
                            App v{device.appVersion}
                          </p>
                        )}
                      </div>
                    </div>
                    <div className="flex items-center gap-2">
                      {device.isLocked && (
                        <div className="p-2 bg-red-100 dark:bg-red-950 rounded-lg">
                          <Lock className="w-4 h-4 text-red-600 dark:text-red-400" />
                        </div>
                      )}
                      <div
                        className={`px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(
                          device.status
                        )}`}
                      >
                        {device.status}
                      </div>
                      <ChevronRight className="w-5 h-5 text-muted-foreground" />
                    </div>
                  </div>
                </Card>
              </Link>
            ))}
          </div>
        )}
      </div>
    </DashboardLayout>
  )
}
