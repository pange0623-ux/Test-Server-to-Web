'use client'

import { useState } from 'react'
import Link from 'next/link'
import { useRouter } from 'next/navigation'
import { authClient } from '@/lib/auth-client'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card } from '@/components/ui/card'

export function AuthForm({ mode }: { mode: 'sign-in' | 'sign-up' }) {
  const router = useRouter()
  const [name, setName] = useState('')
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [error, setError] = useState<string | null>(null)
  const [loading, setLoading] = useState(false)

  const isSignUp = mode === 'sign-up'

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setError(null)
    setLoading(true)

    const { error } = isSignUp
      ? await authClient.signUp.email({ email, password, name })
      : await authClient.signIn.email({ email, password })

    setLoading(false)

    if (error) {
      setError(error.message ?? 'Something went wrong')
      return
    }

    router.push('/')
    router.refresh()
  }

  return (
    <main className="min-h-svh flex items-center justify-center px-4 py-12">
      <div className="w-full max-w-md">
        {/* Header */}
        <div className="text-center mb-8">
          <div className="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-primary/10 mb-4">
            <svg className="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m7.528-4.528a9 9 0 11-12.728 12.728m0 0l3.536-3.536m0 0l3.536 3.536" />
            </svg>
          </div>
          <h1 className="text-3xl font-bold text-foreground mb-2">
            {isSignUp ? 'Get Started' : 'Welcome Back'}
          </h1>
          <p className="text-muted-foreground">
            {isSignUp
              ? 'Create your account to monitor your devices'
              : 'Sign in to your account to continue'}
          </p>
        </div>

        {/* Form Card */}
        <Card className="p-8 border border-border bg-card/50 backdrop-blur-sm">
          <form onSubmit={handleSubmit} className="space-y-5">
            {isSignUp && (
              <div className="space-y-2">
                <Label htmlFor="name" className="text-foreground font-medium">
                  Full Name
                </Label>
                <Input
                  id="name"
                  placeholder="John Doe"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  required
                  autoComplete="name"
                  className="bg-input border-border hover:border-primary/50 focus:border-primary transition-colors"
                />
              </div>
            )}

            <div className="space-y-2">
              <Label htmlFor="email" className="text-foreground font-medium">
                Email Address
              </Label>
              <Input
                id="email"
                type="email"
                placeholder="you@company.com"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
                autoComplete="email"
                className="bg-input border-border hover:border-primary/50 focus:border-primary transition-colors"
              />
            </div>

            <div className="space-y-2">
              <div className="flex items-center justify-between">
                <Label htmlFor="password" className="text-foreground font-medium">
                  Password
                </Label>
                {!isSignUp && (
                  <Link href="#" className="text-xs text-primary hover:text-accent">
                    Forgot password?
                  </Link>
                )}
              </div>
              <Input
                id="password"
                type="password"
                placeholder="••••••••"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
                minLength={8}
                autoComplete={isSignUp ? 'new-password' : 'current-password'}
                className="bg-input border-border hover:border-primary/50 focus:border-primary transition-colors"
              />
              {isSignUp && (
                <p className="text-xs text-muted-foreground">
                  Must be at least 8 characters
                </p>
              )}
            </div>

            {error && (
              <div className="p-3 rounded-lg bg-destructive/10 border border-destructive/20">
                <p className="text-sm text-destructive" role="alert">
                  {error}
                </p>
              </div>
            )}

            <Button
              type="submit"
              disabled={loading}
              className="w-full bg-primary hover:bg-primary/90 text-white font-medium py-2 rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {loading ? (
                <div className="flex items-center gap-2">
                  <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
                  <span>Processing...</span>
                </div>
              ) : isSignUp ? (
                'Create Account'
              ) : (
                'Sign In'
              )}
            </Button>
          </form>

          {/* Divider */}
          <div className="relative my-6">
            <div className="absolute inset-0 flex items-center">
              <div className="w-full border-t border-border/50" />
            </div>
            <div className="relative flex justify-center text-xs uppercase">
              <span className="px-2 bg-card text-muted-foreground">Or continue with</span>
            </div>
          </div>

          {/* Footer Links */}
          <div className="text-center">
            <p className="text-sm text-muted-foreground">
              {isSignUp ? 'Already have an account? ' : "Don't have an account? "}
              <Link
                href={isSignUp ? '/sign-in' : '/sign-up'}
                className="text-primary font-semibold hover:text-accent transition-colors"
              >
                {isSignUp ? 'Sign In' : 'Sign Up'}
              </Link>
            </p>
          </div>
        </Card>

        {/* Footer Text */}
        <p className="text-xs text-muted-foreground text-center mt-6">
          By {isSignUp ? 'signing up' : 'continuing'}, you agree to our{' '}
          <Link href="#" className="hover:text-foreground transition-colors">
            Terms of Service
          </Link>
          {' '}and{' '}
          <Link href="#" className="hover:text-foreground transition-colors">
            Privacy Policy
          </Link>
        </p>
      </div>
    </main>
  )
}
