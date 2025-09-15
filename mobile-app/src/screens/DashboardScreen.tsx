import React from 'react';
import {
  Box,
  VStack,
  HStack,
  Text,
  ScrollView,
  Center,
  Spinner,
  useToast,
} from 'native-base';
import { useQuery } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { forcingService } from '../services/forcingService';
import { DashboardStats } from '../types/Forcing';

interface StatCardProps {
  title: string;
  value: number;
  color: string;
  icon: keyof typeof Ionicons.glyphMap;
}

function StatCard({ title, value, color, icon }: StatCardProps) {
  return (
    <Box
      bg="white"
      p={4}
      borderRadius="lg"
      shadow={2}
      flex={1}
      minH={100}
      justifyContent="center"
      alignItems="center"
    >
      <Ionicons name={icon} size={32} color={color} />
      <Text fontSize="2xl" fontWeight="bold" color="gray.800" mt={2}>
        {value}
      </Text>
      <Text fontSize="sm" color="gray.600" textAlign="center">
        {title}
      </Text>
    </Box>
  );
}

export default function DashboardScreen() {
  const toast = useToast();

  const { data: stats, isLoading, error } = useQuery<DashboardStats>({
    queryKey: ['dashboard-stats'],
    queryFn: () => forcingService.getDashboard(),
    onError: (error: any) => {
      toast.show({
        title: 'Erro ao carregar dashboard',
        description: error.message,
        status: 'error',
      });
    },
  });

  if (isLoading) {
    return (
      <Center flex={1}>
        <Spinner size="large" color="blue.500" />
        <Text mt={4} color="gray.600">
          Carregando dashboard...
        </Text>
      </Center>
    );
  }

  if (error || !stats) {
    return (
      <Center flex={1}>
        <Ionicons name="alert-circle" size={48} color="red" />
        <Text mt={4} color="red.500" textAlign="center">
          Erro ao carregar dados
        </Text>
      </Center>
    );
  }

  return (
    <Box flex={1} bg="gray.50" safeArea>
      <ScrollView px={4} py={4}>
        <VStack space={4}>
          {/* Header */}
          <Box bg="blue.500" p={4} borderRadius="lg">
            <Text color="white" fontSize="xl" fontWeight="bold">
              Dashboard
            </Text>
            <Text color="blue.100" fontSize="sm">
              Visão geral do sistema
            </Text>
          </Box>

          {/* Estatísticas */}
          <VStack space={3}>
            <Text fontSize="lg" fontWeight="semibold" color="gray.800">
              Estatísticas Gerais
            </Text>
            
            {/* Primeira linha */}
            <HStack space={3}>
              <StatCard
                title="Total"
                value={stats.total}
                color="#3B82F6"
                icon="list"
              />
              <StatCard
                title="Pendentes"
                value={stats.pendentes}
                color="#F59E0B"
                icon="time"
              />
            </HStack>

            {/* Segunda linha */}
            <HStack space={3}>
              <StatCard
                title="Liberados"
                value={stats.liberados}
                color="#10B981"
                icon="checkmark-circle"
              />
              <StatCard
                title="Forçados"
                value={stats.forcados}
                color="#F59E0B"
                icon="warning"
              />
            </HStack>

            {/* Terceira linha */}
            <HStack space={3}>
              <StatCard
                title="Solic. Retirada"
                value={stats.solicitacao_retirada}
                color="#8B5CF6"
                icon="send"
              />
              <StatCard
                title="Retirados"
                value={stats.retirados}
                color="#6B7280"
                icon="checkmark-done"
              />
            </HStack>

            {/* Quarta linha */}
            <HStack space={3}>
              <StatCard
                title="Executados"
                value={stats.executados}
                color="#10B981"
                icon="construct"
              />
              <Box flex={1} />
            </HStack>
          </VStack>

          {/* Ações rápidas */}
          <VStack space={3} mt={6}>
            <Text fontSize="lg" fontWeight="semibold" color="gray.800">
              Ações Rápidas
            </Text>
            
            <Box bg="white" p={4} borderRadius="lg" shadow={1}>
              <VStack space={3}>
                <HStack justifyContent="space-between" alignItems="center">
                  <HStack alignItems="center" space={3}>
                    <Ionicons name="add-circle" size={24} color="#3B82F6" />
                    <VStack>
                      <Text fontWeight="medium">Criar Forcing</Text>
                      <Text fontSize="sm" color="gray.600">
                        Novo forcing operacional
                      </Text>
                    </VStack>
                  </HStack>
                  <Ionicons name="chevron-forward" size={20} color="gray" />
                </HStack>
              </VStack>
            </Box>
          </VStack>
        </VStack>
      </ScrollView>
    </Box>
  );
}

