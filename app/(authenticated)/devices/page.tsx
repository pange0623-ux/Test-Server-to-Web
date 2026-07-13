import { auth } from '@/lib/auth'
import { headers } from 'next/headers'
import { redirect } from 'next/navigation'
import { DevicesPageClient } from '@/components/pages/devices-page-client'

export const metadata = {
  title: 'Devices - Device Monitor',
  description: 'Manage and monitor all your devices',
}

export default async function DevicesPage() {
  const session = await auth.api.getSession({ headers: await headers() })

  if (!session?.user) {
    redirect('/sign-in')
  }

  return <DevicesPageClient user={session.user} />
}
