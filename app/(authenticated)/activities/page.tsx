import { auth } from '@/lib/auth'
import { headers } from 'next/headers'
import { redirect } from 'next/navigation'
import { ActivitiesPageClient } from '@/components/pages/activities-page-client'

export const metadata = {
  title: 'Activities - Device Monitor',
  description: 'View all device activities and events',
}

export default async function ActivitiesPage() {
  const session = await auth.api.getSession({ headers: await headers() })

  if (!session?.user) {
    redirect('/sign-in')
  }

  return <ActivitiesPageClient user={session.user} />
}
