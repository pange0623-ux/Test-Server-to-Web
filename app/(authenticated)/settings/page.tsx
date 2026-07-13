import { auth } from '@/lib/auth'
import { headers } from 'next/headers'
import { redirect } from 'next/navigation'
import { SettingsPageClient } from '@/components/pages/settings-page-client'

export const metadata = {
  title: 'Settings - Device Monitor',
  description: 'Manage your account and preferences',
}

export default async function SettingsPage() {
  const session = await auth.api.getSession({ headers: await headers() })

  if (!session?.user) {
    redirect('/sign-in')
  }

  return <SettingsPageClient user={session.user} />
}
