'use client'

import { useState } from 'react'
import { useRouter } from 'next/navigation'
import { authClient } from '@/lib/auth-client'
import { DashboardLayout } from '@/components/layout/dashboard-layout'
import { Card } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Badge } from '@/components/ui/badge'
import {
  User,
  Mail,
  Lock,
  LogOut,
  Bell,
  Palette,
  Shield,
  AlertCircle,
} from 'lucide-react'

interface User {
  id: string
  name: string | null
  email: string
}

export function SettingsPageClient({ user }: { user: User }) {
  const router = useRouter()
  const [loading, setLoading] = useState(false)
  const [showSignOutConfirm, setShowSignOutConfirm] = useState(false)

  const handleSignOut = async () => {
    try {
      setLoading(true)
      await authClient.signOut()
      router.push('/sign-in')
      router.refresh()
    } catch (error) {
      console.error('Failed to sign out:', error)
    } finally {
      setLoading(false)
    }
  }

  return (
    <DashboardLayout user={user}>
      <div className="space-y-6 max-w-2xl">
        <div>
          <h1 className="text-3xl font-bold tracking-tight text-foreground">
            Settings
          </h1>
          <p className="text-sm text-muted-foreground mt-2">
            Manage your account and preferences
          </p>
        </div>

        {/* Account Section */}
        <Card className="p-6">
          <h2 className="text-lg font-semibold mb-6 flex items-center gap-2">
            <User className="w-5 h-5" />
            Account Information
          </h2>

          <div className="space-y-4">
            <div>
              <Label htmlFor="name">Full Name</Label>
              <Input
                id="name"
                type="text"
                value={user.name || ''}
                disabled
                className="bg-muted"
              />
              <p className="text-xs text-muted-foreground mt-1">
                Contact support to change your name
              </p>
            </div>

            <div>
              <Label htmlFor="email">Email Address</Label>
              <Input
                id="email"
                type="email"
                value={user.email}
                disabled
                className="bg-muted"
              />
              <p className="text-xs text-muted-foreground mt-1">
                Your primary email address cannot be changed
              </p>
            </div>

            <div>
              <Label htmlFor="userId">User ID</Label>
              <Input
                id="userId"
                type="text"
                value={user.id}
                disabled
                className="bg-muted font-mono text-xs"
              />
            </div>
          </div>
        </Card>

        {/* Notification Preferences */}
        <Card className="p-6">
          <h2 className="text-lg font-semibold mb-6 flex items-center gap-2">
            <Bell className="w-5 h-5" />
            Notification Preferences
          </h2>

          <div className="space-y-4">
            <div className="flex items-center justify-between p-4 bg-muted/50 rounded-lg">
              <div>
                <p className="font-medium text-foreground">Email Notifications</p>
                <p className="text-sm text-muted-foreground">
                  Receive updates via email
                </p>
              </div>
              <input type="checkbox" defaultChecked className="w-5 h-5" />
            </div>

            <div className="flex items-center justify-between p-4 bg-muted/50 rounded-lg">
              <div>
                <p className="font-medium text-foreground">Push Notifications</p>
                <p className="text-sm text-muted-foreground">
                  Receive browser notifications
                </p>
              </div>
              <input type="checkbox" defaultChecked className="w-5 h-5" />
            </div>

            <div className="flex items-center justify-between p-4 bg-muted/50 rounded-lg">
              <div>
                <p className="font-medium text-foreground">Alert on Device Offline</p>
                <p className="text-sm text-muted-foreground">
                  Notify when a device goes offline
                </p>
              </div>
              <input type="checkbox" defaultChecked className="w-5 h-5" />
            </div>

            <div className="flex items-center justify-between p-4 bg-muted/50 rounded-lg">
              <div>
                <p className="font-medium text-foreground">Location Alerts</p>
                <p className="text-sm text-muted-foreground">
                  Notify on unusual location changes
                </p>
              </div>
              <input type="checkbox" defaultChecked className="w-5 h-5" />
            </div>
          </div>
        </Card>

        {/* Display & Appearance */}
        <Card className="p-6">
          <h2 className="text-lg font-semibold mb-6 flex items-center gap-2">
            <Palette className="w-5 h-5" />
            Display & Appearance
          </h2>

          <div className="space-y-4">
            <div>
              <Label htmlFor="theme">Theme</Label>
              <select
                id="theme"
                defaultValue="dark"
                className="w-full px-3 py-2 border border-border rounded-md bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary"
              >
                <option value="light">Light</option>
                <option value="dark">Dark</option>
                <option value="system">System Default</option>
              </select>
            </div>

            <p className="text-sm text-muted-foreground">
              Refresh the page to apply theme changes
            </p>
          </div>
        </Card>

        {/* Security */}
        <Card className="p-6">
          <h2 className="text-lg font-semibold mb-6 flex items-center gap-2">
            <Shield className="w-5 h-5" />
            Security
          </h2>

          <div className="space-y-4">
            <Button variant="outline" className="w-full justify-start" disabled>
              <Lock className="w-4 h-4 mr-2" />
              Change Password
              <Badge variant="outline" className="ml-auto">
                Coming Soon
              </Badge>
            </Button>

            <Button variant="outline" className="w-full justify-start" disabled>
              <Shield className="w-4 h-4 mr-2" />
              Enable Two-Factor Authentication
              <Badge variant="outline" className="ml-auto">
                Coming Soon
              </Badge>
            </Button>
          </div>
        </Card>

        {/* Danger Zone */}
        <Card className="p-6 border-red-200 dark:border-red-900/50">
          <h2 className="text-lg font-semibold mb-6 flex items-center gap-2 text-red-600 dark:text-red-400">
            <AlertCircle className="w-5 h-5" />
            Danger Zone
          </h2>

          <div className="space-y-4">
            {showSignOutConfirm ? (
              <div className="p-4 bg-red-50 dark:bg-red-950/30 rounded-lg border border-red-200 dark:border-red-900">
                <p className="text-sm font-medium text-red-900 dark:text-red-300 mb-3">
                  Are you sure you want to sign out? You will need to sign in again to access your account.
                </p>
                <div className="flex gap-2">
                  <Button
                    variant="destructive"
                    size="sm"
                    onClick={handleSignOut}
                    disabled={loading}
                  >
                    {loading ? 'Signing out...' : 'Yes, Sign Out'}
                  </Button>
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => setShowSignOutConfirm(false)}
                  >
                    Cancel
                  </Button>
                </div>
              </div>
            ) : (
              <Button
                variant="destructive"
                className="w-full justify-start"
                onClick={() => setShowSignOutConfirm(true)}
              >
                <LogOut className="w-4 h-4 mr-2" />
                Sign Out
              </Button>
            )}
          </div>
        </Card>

        {/* API Keys (placeholder) */}
        <Card className="p-6 opacity-50">
          <h2 className="text-lg font-semibold mb-6">API Keys</h2>
          <p className="text-sm text-muted-foreground">
            API integration coming soon. You&apos;ll be able to manage API keys and webhooks here.
          </p>
        </Card>
      </div>
    </DashboardLayout>
  )
}
