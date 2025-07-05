import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, BehaviorSubject } from 'rxjs';
import { tap } from 'rxjs/operators';
import { environment } from '../environments/environment';

export interface User {
  id: number;
  uuid: string;
  email: string;
  full_name: string;
  is_active: boolean;
  roles: { code: string; title: string }[];
}

export interface AuthResponse {
  user: User;
  token: string;
}

@Injectable({ providedIn: 'root' })
export class AuthService {
  private apiUrl = `${environment.baseApiUrl}/api/auth`;
  private tokenKey = 'fdms_token';
  private userSubject = new BehaviorSubject<User | null>(null);
  public user$ = this.userSubject.asObservable();

  constructor(private http: HttpClient) {
    const token = this.getToken();
    if (token) {
      this.profile().subscribe();
    }
  }

  login(email: string, password: string): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${this.apiUrl}/login`, { email, password }).pipe(
      tap((res) => {
        this.setToken(res.token);
        this.userSubject.next(res.user);
      })
    );
  }

  register(email: string, password: string, full_name: string): Observable<{ user: User; message: string }> {
    return this.http.post<{ user: User; message: string }>(`${this.apiUrl}/register`, { email, password, full_name }).pipe(
      tap((res) => {
        // Optionally auto-login after registration
        // this.setToken(res.token);
        // this.userSubject.next(res.user);
      })
    );
  }

  profile(): Observable<{ user: User }> {
    return this.http.get<{ user: User }>(`${this.apiUrl}/profile`, {
      headers: this.getAuthHeaders()
    }).pipe(
      tap((res) => {
        this.userSubject.next(res.user);
      })
    );
  }

  logout() {
    this.removeToken();
    this.userSubject.next(null);
  }

  getToken(): string | null {
    return localStorage.getItem(this.tokenKey);
  }

  setToken(token: string) {
    localStorage.setItem(this.tokenKey, token);
  }

  removeToken() {
    localStorage.removeItem(this.tokenKey);
  }

  getAuthHeaders(): HttpHeaders {
    const token = this.getToken();
    return token ? new HttpHeaders({ Authorization: `Bearer ${token}` }) : new HttpHeaders();
  }

  isLoggedIn(): boolean {
    return !!this.getToken();
  }
} 