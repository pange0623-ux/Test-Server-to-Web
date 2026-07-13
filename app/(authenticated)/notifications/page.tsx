import { auth } from '@/lib/auth'
import { headers } from 'next/headers'
import { redirect } from 'next/navigation'
import { NotificationsPageClient } from '@/components/pages/notifications-page-client'

export const metadata = {
  title: 'Notifications - Device Monitor',
  description: 'View all your notifications',
}

export default async function NotificationsPage() {
  const session = await auth.api.getSession({ headers: await headers() })

  if (!session?.user) {
    redirect('/sign-in')
  }

  return <NotificationsPageClient user={session.user} />
}
